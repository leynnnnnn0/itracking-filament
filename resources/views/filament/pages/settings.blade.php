<x-filament-panels::page>
    <div style="gap: 24px; display: grid; ">
        <div style="background-color: white; padding: 20px;" class="flex-1 rounded-lg shadow-lg">
            <h1 class="font-bold text-lg mb-2">Create a Database Backup</h1>
            <p class="text-sm text-gray-600 mb-4">
                Safeguard your important data by creating a comprehensive database backup.
                This process ensures you can restore your system to a previous state if needed.
            </p>
            <x-filament::modal id="database-backup" alignment="center" icon="heroicon-o-exclamation-triangle"
                :close-button="true">
                <x-slot name="trigger">
                    <x-filament::button color="info">
                        Create Backup
                    </x-filament::button>
                </x-slot>
                <x-slot name="heading" color="info">
                    Create a Backup
                </x-slot>
                <section class="text-center">
                    By click the confirm button the system will generate a database backup file that will be stored on the system and it will be the most latest database content
                </section>
                <x-slot name="footer">
                    <div class="flex justify-center">
                        <x-filament::button color="primary" wire:click="performDatabaseBackup">
                            Confirm
                        </x-filament::button>
                    </div>
                </x-slot>

                {{-- Modal content --}}
            </x-filament::modal>
        </div>
        <div style="background-color: white; padding: 20px;" class="flex-1 rounded-lg shadow-lg">
            <h1 class="font-bold text-lg mb-2">Restore Database</h1>
            <p class="text-sm text-gray-600 mb-4">
                Recover your system from a previous backup. Select and restore your database
                to a specific point in time, ensuring data integrity and system reliability.
            </p>
            <button
                wire:click="confirmDatabaseRestore"
                style="background-color: #ed8936; color: white; padding: 10px 20px;"
                class="rounded-lg font-bold text-sm hover:opacity-90 transition-all">
                Restore Database
            </button>
        </div>
        <div style="background-color: white; padding: 20px;" class="flex-1 rounded-lg shadow-lg">
            <h1 class="font-bold text-lg mb-2">User Manual</h1>
            <p class="text-sm text-gray-600 mb-4">
                Access the comprehensive user manual to guide you through the system's features,
                functionalities, and best practices for optimal usage.
            </p>
            <button
                wire:click="downloadUserManual"
                style="background-color: #4299e1; color: white; padding: 10px 20px;"
                class="rounded-lg font-bold text-sm hover:opacity-90 transition-all">
                Download Manual
            </button>
        </div>
    </div>
</x-filament-panels::page>