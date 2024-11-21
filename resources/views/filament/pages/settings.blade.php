<x-filament-panels::page>
    <div style="gap: 24px; display: grid; ">
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
            </x-filament::modal>
        </div>

        <div style="background-color: white; padding: 20px;" class="flex-1 rounded-lg shadow-lg">
            <h1 class="font-bold text-lg mb-2">Restore Database</h1>
            <p class="text-sm text-gray-600 mb-4">
                Recover your system from a previous backup. Select and restore your database
                to a specific point in time, ensuring data integrity and system reliability.
            </p>

            <div class="flex items-center space-x-4">
                <input
                    type="file"
                    wire:model="backupFile"
                    accept=".sql"
                    class="flex-grow">
                <x-filament::modal color="danger" icon="heroicon-o-exclamation-triangle" id="database-restore" alignment="center" :close-button="true">
                    <x-slot name="trigger">
                        <x-filament::button color="warning" :disabled="!$backupFile">
                            Restore Backup
                        </x-filament::button>
                    </x-slot>
                    <x-slot name="heading" color="warning">
                        Restore Backup
                    </x-slot>
                    <section class="text-center">
                        By clicking the confirm button, the current database will be replaced by the uploaded SQL backup file.
                        You will be logged out of the current session and redirected to the login page.
                    </section>
                    <x-slot name="footer">
                        <div class="flex justify-center">
                            <x-filament::button
                                color="danger"
                                wire:click="performDatabaseRestore"
                                :disabled="!$backupFile">
                                Restore Database Data
                            </x-filament::button>
                        </div>
                    </x-slot>
                </x-filament::modal>
            </div>
        </div>
    </div>
</x-filament-panels::page>