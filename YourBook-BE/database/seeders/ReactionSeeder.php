<?php

namespace Database\Seeders;

use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reaction::createFromName('like')->save();
        Reaction::createFromName('love')->save();
    }
}
