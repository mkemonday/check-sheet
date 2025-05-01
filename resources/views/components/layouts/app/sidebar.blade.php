<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>


        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>

            @canany(['view-daily-check', 'delete-daily-check', 'create-daily-check', 'edit-daily-check'])
                <flux:navlist.group :heading="__('Daily Check')" class="grid">
                    <flux:navlist.item icon="book-open-text" :href="route('daily.daily-check-matrix')"
                        :current="request()->routeIs('daily.daily-check-matrix')" wire:navigate>{{ __('Daily Check') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            @endcanany

            @canany(['view-area', 'view-check-method', 'view-check-item'])
            <flux:navlist.group :heading="__('Setup')" class="grid">
                @canany(['view-area', 'delete-area', 'create-area', 'edit-area'])
                <flux:navlist.item :href="route('setup.areas')" :current="request()->routeIs('setup.areas')"
                    wire:navigate>Area</flux:navlist.item>
                @endcanany
                @canany(['view-check-method', 'delete-check-method', 'create-check-method', 'edit-check-method'])
                <flux:navlist.item :href="route('setup.check-methods')"
                    :current="request()->routeIs('setup.check-methods')" wire:navigate>Method</flux:navlist.item>
                @endcanany
                @canany(['view-check-item', 'delete-check-item', 'create-check-item', 'edit-check-item'])
                <flux:navlist.item :href="route('setup.check-items')"
                    :current="request()->routeIs('setup.check-items')" wire:navigate>Check Item</flux:navlist.item>
                @endcanany
            </flux:navlist.group>
            @endcanany

            @canany(['view-users', 'admin-only'])
                <flux:navlist.group :heading="__('User Management')" class="grid">
                    @canany(['view-users', 'delete-users', 'create-users', 'edit-users'])
                    <flux:navlist.item :href="route('users.manage')"
                        :current="request()->routeIs('users.manage')" wire:navigate>Users</flux:navlist.item>
                    <flux:navlist.item :href="route('users.assign-roles')"
                    :current="request()->routeIs('users.assign-roles')" wire:navigate>User Roles</flux:navlist.item>
                    @endcanany
                    @canany(['admin-only'])
                    <flux:navlist.item :href="route('roles.manage')" :current="request()->routeIs('roles.manage')"
                        wire:navigate>Roles</flux:navlist.item>
                    <flux:navlist.item :href="route('permissions.manage')"
                        :current="request()->routeIs('permissions.manage')" wire:navigate>Permissions</flux:navlist.item>
                    @endcanany
                </flux:navlist.group>
            @endcanany
            
            


        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}
    @livewireScripts
    @stack('scripts')
    @fluxScripts
</body>

</html>
