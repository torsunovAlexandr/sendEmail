<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use stdClass;


class MailController extends Controller
{
    CONST SECOND_PER_DAY = 86400;
    /**
     * Получение списка пользователей для рассылки сообщений
     * @return array
     */
    public function getUsers(): array
    {
        $firstDatePreviousMonth = Carbon::parse('first day of previous month')->format('Y-m-d');
        $lastDatePreviousMonth = Carbon::parse('last day of previous month')->format('Y-m-d');

        $sql = "
            select
                CONCAT_WS(' ',u.first_name,u.second_name) as name,
                u.email,
                count(p1.user_id) as countAuthInLastMonth,
                CASE WHEN ua.user_id IS NULL THEN 0 ELSE 1 END authInPromotionPeriod
            FROM users u
            LEFT JOIN (
                SELECT
                    ls.user_id
                FROM login_source ls
                WHERE (ls.tms > :firstDatePreviousMonth AND ls.tms < :lastDatePreviousMonth)
            ) p1 ON p1.user_id = u.id
            LEFT JOIN user_actions ua on ua.user_id = u.id
            LEFT JOIN actions a on a.id = ua.action_id
            GROUP BY
                name,
                u.email,ua.user_id

        ";

        return DB::select(DB::raw($sql), array(
            'firstDatePreviousMonth' => $firstDatePreviousMonth,
            'lastDatePreviousMonth' => $lastDatePreviousMonth,
        ));

    }

    /**
     * Получение акции
     * @return object
     */
    public function getAction(): object
    {
         return DB::table('actions')
            ->select('title', 'date_end')
            ->first();
    }

    /**
     * Распределение пользователей по разным группам для рассылки
     */
    public function distributeUsersIntoGroups()
    {
        $users = $this->getUsers();
        $delayForSendEmail = self::SECOND_PER_DAY / count($users);
        //ввёл вторую переменную для перерасчёта в цикле
        $delay = $delayForSendEmail;

        //раскидывает юзеров по группам
        foreach ($users as $user) {
            if($user->countAuthInLastMonth > 0) {
                if ($user->authInPromotionPeriod == 0) {
                    $this->sendEmail($user, 'GroupC', $delay);
                    //Устанаваливаем между сообщениями равные промежутки времени
                    $delay+= $delayForSendEmail;
                    continue;
                } else {
                    if($user->countAuthInLastMonth >= 2) {
                        $this->sendEmail($user, 'GroupB', $delay);
                        $delay+= $delayForSendEmail;
                        continue;
                    }
                    $this->sendEmail($user, 'GroupA', $delay);
                    $delay+= $delayForSendEmail;
                }

            }
        }
        echo '<pre>',print_r( 'Сообщения поставлены в очередь для отправки'),'</pre>';
    }

    /**
     * Отправить сообщения пользователям
     */
    public function sendEmail($user, $group, $delay)
    {
        $data = new stdClass();
        $data->name = $user->name;
        $data->group = $group;
        $data->title = null;
        $data->date_end = null;
        if($group == 'GroupC') {
            $action = $this->getAction();
            $date_end = new Carbon($action->date_end);
            $data->title = $action->title;
            $data->date_end = $date_end->format('d.m.Y');
        }

        $when = Carbon::now()->addSeconds($delay);
        Mail::to($user->email)
            ->later($when,new SendEmail($data));
    }
}
