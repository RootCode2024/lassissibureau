<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le compte administrateur principal
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@lassissi.com',
            'password' => Hash::make('password'), // ⚠️ À CHANGER EN PRODUCTION
            'email_verified_at' => now(),
        ]);

        $admin->assignRole(UserRole::ADMIN->value);

        $this->command->info('✅ Utilisateur admin créé avec succès!');
        $this->command->warn('   Email: admin@lassissi.com');
        $this->command->warn('   Mot de passe: password');
        $this->command->error('   ⚠️  PENSEZ À CHANGER LE MOT DE PASSE EN PRODUCTION!');

        // Créer un vendeur de test (optionnel)
        $vendeur = User::create([
            'name' => 'Vendeur Test',
            'email' => 'vendeur@lassissi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $vendeur->assignRole(UserRole::VENDEUR->value);

        $this->command->info('✅ Utilisateur vendeur créé avec succès!');
        $this->command->warn('   Email: vendeur@lassissi.com');
        $this->command->warn('   Mot de passe: password');
    }
}
