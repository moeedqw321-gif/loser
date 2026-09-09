<?php
use Cron\Cron;
class Bet_crons {
    public function index () {
        $job1 = new \Cron\Job\ShellJob();
        $job1->setCommand('php ' . FCPATH . 'index.php bets/api/GetUpcomingOdds');
        $job1->setSchedule(new \Cron\Schedule\CrontabSchedule('*/1 * * * *'));
        $resolver = new \Cron\Resolver\ArrayResolver();
        $resolver->addJob($job1);
        $cron = new \Cron\Cron();
        $cron->setExecutor(new \Cron\Executor\Executor());
        $cron->setResolver($resolver);
        $cron->run();
        dd($cron);
    }
}
?>
