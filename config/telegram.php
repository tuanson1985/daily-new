<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Your Telegram Bots
    |--------------------------------------------------------------------------
    | You may use multiple bots at once using the manager class. Each bot
    | that you own should be configured here.
    |
    | Here are each of the telegram bots config parameters.
    |
    | Supported Params:
    |
    | - name: The *personal* name you would like to refer to your bot as.
    |
    |       - username: Your Telegram Bot's Username.
    |                       Example: (string) 'BotFather'.
    |
    |       - token:    Your Telegram Bot's Access Token.
                        Refer for more details: https://core.telegram.org/bots#botfather
    |                   Example: (string) '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11'.
    |
    |       - commands: (Optional) Commands to register for this bot,
    |                   Supported Values: "Command Group Name", "Shared Command Name", "Full Path to Class".
    |                   Default: Registers Global Commands.
    |                   Example: (array) [
    |                       'admin', // Command Group Name.
    |                       'status', // Shared Command Name.
    |                       Acme\Project\Commands\BotFather\HelloCommand::class,
    |                       Acme\Project\Commands\BotFather\ByeCommand::class,
    |             ]
    */
    'bots'                         => [
        'mybot' => [
            'username'            => 'TelegramBot',
            'token'               => env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN'),
            'certificate_path'    => env('TELEGRAM_CERTIFICATE_PATH', 'YOUR-CERTIFICATE-PATH'),
            'webhook_url'         => env('TELEGRAM_WEBHOOK_URL', 'YOUR-BOT-WEBHOOK-URL'),
            'commands'            => [
                //Acme\Project\Commands\MyTelegramBot\BotCommand::class
            ],
            'channel_id'          => env('TELEGRAM_CHANNEL_ID', ''),
            'channel_id_report'          => env('TELEGRAM_REPORT_CHANNEL_ID', '')    ,

            //channel bot bán ngọc
            'channel_id_balance_daily'          => env('TELEGRAM_CHANNEL_ID_BALANCE_DAILY', ''),
            'channel_bot_balance_roblox'          => env('TELEGRAM_CHANNEL_BOT_BALANCE_ROBLOX', ''),
            'channel_id_matitem_nrogem'          => env('TELEGRAM_CHANNEL_ID_MATITEM_NROGEM', ''),
            'channel_ban_ngoc'          => env('CHANEL_BAN_NGOC', ''),
            'channel_noty_github'          => env('CHANEL_NOTY_GITHUB', ''),
            'channel_noty_minigame'          => env('CHANEL_NOTY_MINIGAME', ''),
            'channel_noty_congtien'          => env('THONG_BAO_CONG_TIEN', ''),
            'channel_noty_congvatpham'          => env('CHANEL_NOTIFY_VATPHAM', ''),
            'channel_noty_telecom'          => env('CHANEL_NOTI_TELECOM', ''),
            'channel_noty_access_user'          => env('CHANEL_NOTY_ACCES_USER', ''),
            'channel_notify_balance_tichhop_net'          => env('TELEGRAM_NOTIFY_BALANCE_TICHHOP_NET', ''),
            'channel_notify_ncc'          => env('TELEGRAM_NOTIFY_NCC', ''),
            'channel_notify_roles'          => env('TELEGRAM_NOTIFY_ROLES', ''),
            'channel_notify_balance_bot_roblox'          => env('TELEGRAM_NOTIFY_BALANCE_BOT_ROBLOX', ''),
            'channel_notify_check_balance_user'          => env('TELEGRAM_NOTIFY_CHECK_BALANCE_USER', ''),
            'channel_noty_setting'          => env('TELEGRAM_NOTIFY_SETTING', ''),
            'channel_noty_expired_time_domain'          => env('TELEGRAM_NOTIFY_EXPIRED_TIME_DOMAIN', ''),
            'channel_bot_roblox'          => env('TELEGRAM_BOT_ROBLOX', ''),
            'channel_bot_huge_psx_roblox'          => env('TELEGRAM_BOT_HUGE_PSX_ROBLOX', ''),
            'channel_bot_change_current_password'          => env('TELEGRAM_CHANGE_CURRENT_PASSWORD', ''),
            'channel_bot_check_cookie_roblox'          => env('TELEGRAM_BOT_CHECK_COOKIE_ROBLOX', ''),
            'channel_bot_update_service'          => env('TELEGRAM_BOT_UPDATE_SERVICE', ''),
            'channel_bot_price_service'          => env('TELEGRAM_BOT_PRICE_SERVICE', ''),
            'channel_bot_update_unit'          => env('TELEGRAM_BOT_UPDATE_UNIT', ''),
            'channel_bot_anime_defender'          => env('TELEGRAM_BOT_UPDATE_UNIT', ''),
            'channel_bot_telegram_pemission_update'          => env('TELEGRAM_PEMISSION_UPDATE', ''),
            'channel_bot_roblox_pet99_san'          => env('TELEGRAM_BOT_ROBLOX_PET99_SAN', ''),
            'channel_bot_global'          => env('TELEGRAM_BOT_GLOBAL', ''),
            'channel_bot_add_roblox'          => env('TELEGRAM_BOT_ADD_ROBLOX', ''),
            'channel_bot_rbx_balance_roblox'          => env('TELEGRAM_BOT_RBX_BALANCE_ROBLOX', ''),
            'channel_bot_defragment_roblox'          => env('TELEGRAM_BOT_DEFRAGMENT_ROBLOX', ''),
            'channel_bot_warning_rpx'          => env('TELEGRAM_BOT_WARNING_RPX', ''),

        ],

        //        'mySecondBot' => [
        //            'username'  => 'AnotherTelegram_Bot',
        //            'token' => '123456:abc',
        //        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Bot Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the bots you wish to use as
    | your default bot for regular use.
    |
    */
    'default'                      => 'mybot',

    /*
    |--------------------------------------------------------------------------
    | Asynchronous Requests [Optional]
    |--------------------------------------------------------------------------
    |
    | When set to True, All the requests would be made non-blocking (Async).
    |
    | Default: false
    | Possible Values: (Boolean) "true" OR "false"
    |
    */
    'async_requests'               => env('TELEGRAM_ASYNC_REQUESTS', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Handler [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use a custom HTTP Client Handler.
    | Should be an instance of \Telegram\Bot\HttpClients\HttpClientInterface
    |
    | Default: GuzzlePHP
    |
    */
    'http_client_handler'          => null,

    /*
    |--------------------------------------------------------------------------
    | Resolve Injected Dependencies in commands [Optional]
    |--------------------------------------------------------------------------
    |
    | Using Laravel's IoC container, we can easily type hint dependencies in
    | our command's constructor and have them automatically resolved for us.
    |
    | Default: true
    | Possible Values: (Boolean) "true" OR "false"
    |
    */
    'resolve_command_dependencies' => true,

    /*
    |--------------------------------------------------------------------------
    | Register Telegram Global Commands [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use the SDK's built in command handler system,
    | You can register all the global commands here.
    |
    | Global commands will apply to all the bots in system and are always active.
    |
    | The command class should extend the \Telegram\Bot\Commands\Command class.
    |
    | Default: The SDK registers, a help command which when a user sends /help
    | will respond with a list of available commands and description.
    |
    */
    'commands'                     => [
        Telegram\Bot\Commands\HelpCommand::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Command Groups [Optional]
    |--------------------------------------------------------------------------
    |
    | You can organize a set of commands into groups which can later,
    | be re-used across all your bots.
    |
    | You can create 4 types of groups:
    | 1. Group using full path to command classes.
    | 2. Group using shared commands: Provide the key name of the shared command
    | and the system will automatically resolve to the appropriate command.
    | 3. Group using other groups of commands: You can create a group which uses other
    | groups of commands to bundle them into one group.
    | 4. You can create a group with a combination of 1, 2 and 3 all together in one group.
    |
    | Examples shown below are by the group type for you to understand each of them.
    */
    'command_groups'               => [
        /* // Group Type: 1
           'commmon' => [
                Acme\Project\Commands\TodoCommand::class,
                Acme\Project\Commands\TaskCommand::class,
           ],
        */

        /* // Group Type: 2
           'subscription' => [
                'start', // Shared Command Name.
                'stop', // Shared Command Name.
           ],
        */

        /* // Group Type: 3
            'auth' => [
                Acme\Project\Commands\LoginCommand::class,
                Acme\Project\Commands\SomeCommand::class,
            ],

            'stats' => [
                Acme\Project\Commands\UserStatsCommand::class,
                Acme\Project\Commands\SubscriberStatsCommand::class,
                Acme\Project\Commands\ReportsCommand::class,
            ],

            'admin' => [
                'auth', // Command Group Name.
                'stats' // Command Group Name.
            ],
        */

        /* // Group Type: 4
           'myBot' => [
                'admin', // Command Group Name.
                'subscription', // Command Group Name.
                'status', // Shared Command Name.
                'Acme\Project\Commands\BotCommand' // Full Path to Command Class.
           ],
        */
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared Commands [Optional]
    |--------------------------------------------------------------------------
    |
    | Shared commands let you register commands that can be shared between,
    | one or more bots across the project.
    |
    | This will help you prevent from having to register same set of commands,
    | for each bot over and over again and make it easier to maintain them.
    |
    | Shared commands are not active by default, You need to use the key name to register them,
    | individually in a group of commands or in bot commands.
    | Think of this as a central storage, to register, reuse and maintain them across all bots.
    |
    */
    'shared_commands'              => [
        // 'start' => Acme\Project\Commands\StartCommand::class,
        // 'stop' => Acme\Project\Commands\StopCommand::class,
        // 'status' => Acme\Project\Commands\StatusCommand::class,
    ],
];
