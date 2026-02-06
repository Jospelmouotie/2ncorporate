<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'make:admin';

    // Description de la commande
    protected $description = 'Crée un nouvel utilisateur avec les droits administrateur';

    public function handle()
    {
        // Demander les informations interactivement
        $name = $this->ask('Nom de l\'administrateur');
        $email = $this->ask('Adresse email');
        $password = $this->secret('Mot de passe');

        // Validation basique
        if (User::where('email', $email)->exists()) {
            $this->error('Erreur : Un utilisateur avec cet email existe déjà !');
            return;
        }

        // Création de l'utilisateur
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true, // On force le statut admin
        ]);

        $this->info("---------------------------------------");
        $this->info("Succès ! L'administrateur {$name} a été créé.");
        $this->info("Email : {$email}");
        $this->info("---------------------------------------");
    }
}
