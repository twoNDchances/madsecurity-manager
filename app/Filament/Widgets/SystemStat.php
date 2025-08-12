<?php

namespace App\Filament\Widgets;

use App\Models\Defender;
use App\Models\Report;
use App\Services\FilamentWidgetService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class SystemStat extends BaseWidget
{
    private static $widget = FilamentWidgetService::class;

    protected function getStats(): array
    {
        return [
            self::getActivationDefenders(),
            self::getApplicationGroups(),
            self::getImplementationDecisions(),
            self::getReportsRecieved(),
        ];
    }

    private static function getActivationDefenders()
    {
        $totalDefenders = Defender::count();
        $activationDefenders = Defender::where('last_status', true)->count();
        $changedToday = Defender::whereDate('updated_at', Carbon::today())->count();
        return self::$widget::stat(
            'Activation Defenders',
            "$activationDefenders/$totalDefenders",
            'heroicon-o-server-stack',
            "$changedToday changes today",
        )
        ->color('purple');
    }

    private static function getApplicationGroups()
    {
        $totalAssignmentGroups = DB::table('defenders_groups')->count();
        $applicationGroups = DB::table('defenders_groups')->where('status', true)->count();
        $changedToday = DB::table('defenders_groups')->whereDate('updated_at', Carbon::today())->count();
        return self::$widget::stat(
            'Application Groups',
            "$applicationGroups/$totalAssignmentGroups",
            'heroicon-o-rectangle-stack',
            "$changedToday changes today"
        )
        ->color('success');
    }

    private static function getImplementationDecisions()
    {
        $totalAssignmentDecisions = DB::table('defenders_decisions')->count();
        $applicationDecisions = DB::table('defenders_decisions')->where('status', true)->count();
        $changedToday = DB::table('defenders_decisions')->whereDate('updated_at', Carbon::today())->count();
        return self::$widget::stat(
            'Implementation Decisions ',
            "$applicationDecisions/$totalAssignmentDecisions",
            'heroicon-o-scale',
            "$changedToday changes today"
        )
        ->color('danger');
    }

    private static function getReportsRecieved()
    {
        $reports = Report::where('created_at', Carbon::today())->count();
        return self::$widget::stat(
            'Reports Recieved',
            $reports,
            'heroicon-o-flag',
            'Today',
        )
        ->color('warning');
    }
}
