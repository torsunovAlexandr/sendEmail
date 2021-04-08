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

        return db::select(db::raw($sql), array(
            'firstDatePreviousMonth' => $firstDatePreviousMonth,
            'lastDatePreviousMonth' => $lastDatePreviousMonth,
        ));

    }

    public function getAction()
    {
         return DB::table('actions')
            ->select('title', 'date_end')
            ->first();
    }

    public function distributeUsersIntoGroups()
    {
        $users = $this->getUsers();
        echo '<pre>',print_r( $users),'</pre>';

        $delayForSendEmail = self::SECOND_PER_DAY / count($users);
        //ввёл вторую переменную для перерасчёта в цикле
        $delay = $delayForSendEmail;

        //раскидывает юзеров по группам
        foreach ($users as $user) {
            echo '<pre>',print_r( $delay),'</pre>';
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
    }

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
        echo "Email for {$user->name} {$group} sent!!!";
    }

}
