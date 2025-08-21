<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\Decision;
use App\Models\Defender;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Policy;
use App\Models\Report;
use App\Models\Rule;
use App\Models\Tag;
use App\Models\Token;
use App\Models\User;
use App\Models\Wordlist;
use App\Observers\AssetObserver;
use App\Observers\DecisionObserver;
use App\Observers\DefenderObserver;
use App\Observers\GroupObserver;
use App\Observers\PermissionObserver;
use App\Observers\PolicyObserver;
use App\Observers\ReportObserver;
use App\Observers\RuleObserver;
use App\Observers\TagObserver;
use App\Observers\TokenObserver;
use App\Observers\UserObserver;
use App\Observers\WordlistObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Asset::observe(AssetObserver::class);
        Decision::observe(DecisionObserver::class);
        Defender::observe(DefenderObserver::class);
        Group::observe(GroupObserver::class);
        Permission::observe(PermissionObserver::class);
        Report::observe(ReportObserver::class);
        Policy::observe(PolicyObserver::class);
        Rule::observe(RuleObserver::class);
        Tag::observe(TagObserver::class);
        Token::observe(TokenObserver::class);
        User::observe(UserObserver::class);
        Wordlist::observe(WordlistObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
