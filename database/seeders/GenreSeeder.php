<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Utils\FileUtility;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Genre::exists()) {
            foreach ($this->data() as $record) {
                $genre = new Genre();
                $genre->setTranslations('name', json_decode($record['name'], true));

                if (isset($record['icon'])) {
                    $genre->icon = $this->storeImage('seeders/genre/icons/', $record['icon'], 'image/svg+xml');
                    $genre->placeholder = $this->storeImage(
                        'seeders/genre/placeholders/',
                        $record['placeholder'],
                        'image/jpeg'
                    );
                }

                $genre->save();
            }
        }
    }

    private function storeImage(string $folder, string $icon, string $mimetype): UploadedFile
    {
        $absolutePath = storage_path($folder . $icon);

        return FileUtility::createTemporaryFileByUrl($absolutePath, $icon, $mimetype);
    }

    private function data(): array
    {
        return [
            ['name' => '{"en":"Action", "no":"Handling"}', 'icon' => 'action.svg', 'placeholder' => 'action.jpg'],
            ['name' => '{"en":"Adventure", "no":"Eventyr"}', 'icon' => 'adventure.svg', 'placeholder' => 'adventure.jpg'],
            ['name' => '{"en":"Biography", "no":"Biografi"}', 'icon' => 'biography.svg', 'placeholder' => 'biography.jpg'],
            ['name' => '{"en":"Comedy", "no":"Komedie"}', 'icon' => 'comedy.svg', 'placeholder' => 'comedy.jpg'],
            ['name' => '{"en":"Crime", "no":"Forbrytelse"}', 'icon' => 'crime.svg', 'placeholder' => 'crime.jpg'],
            ['name' => '{"en":"Dance", "no":"Danse"}', 'icon' => 'dance.svg', 'placeholder' => 'dance.jpg'],
            ['name' => '{"en":"Disaster", "no":"Katastrofe"}', 'icon' => 'disaster.svg', 'placeholder' => 'disaster.jpg'],
            ['name' => '{"en":"Documentary", "no":"Dokumentar"}', 'icon' => 'documentary.svg', 'placeholder' => 'documentary.jpg'],
            ['name' => '{"en":"Drama", "no":"Drama"}', 'icon' => 'drama.svg', 'placeholder' => 'drama.jpg'],
            ['name' => '{"en":"Family", "no":"Familie"}', 'icon' => 'family.svg', 'placeholder' => 'family.jpg'],
            ['name' => '{"en":"Fantasy", "no":"Fantasi"}', 'icon' => 'fantasy.svg', 'placeholder' => 'fantasy.jpg'],
            ['name' => '{"en":"Found Footage", "no":"Fant opptak"}', 'icon' => 'found_footage.svg', 'placeholder' => 'found_fotage.jpg'],
            ['name' => '{"en":"Historical", "no":"Historisk"}', 'icon' => 'historical.svg', 'placeholder' => 'historical.jpg'],
            ['name' => '{"en":"Horror", "no":"Skrekk"}', 'icon' => 'horror.svg', 'placeholder' => 'horror.jpg'],
            ['name' => '{"en":"Independent", "no":"Uavhengig"}', 'icon' => 'independent.svg', 'placeholder' => 'independent.jpg'],
            ['name' => '{"en":"Legal", "no":"Lovlig"}', 'icon' => 'legal.svg', 'placeholder' => 'legal.jpg'],
            ['name' => '{"en":"Martial Arts", "no":"Kampsport"}', 'icon' => 'martial_arts.svg', 'placeholder' => 'martial_arts.jpg'],
            ['name' => '{"en":"Musical", "no":"Musikalsk"}', 'icon' => 'musical.svg', 'placeholder' => 'musical.jpg'],
            ['name' => '{"en":"Mystery", "no":"Mysterium"}', 'icon' => 'mystery.svg', 'placeholder' => 'mystery.jpg'],
            ['name' => '{"en":"Political", "no":"Politisk"}', 'icon' => 'political.svg', 'placeholder' => 'political.jpg'],
            ['name' => '{"en":"Romance", "no":"Romanse"}', 'icon' => 'romance.svg', 'placeholder' => 'romance.jpg'],
            ['name' => '{"en":"Satire", "no":"Satire"}', 'icon' => 'satire.svg', 'placeholder' => 'satire.jpg'],
            ['name' => '{"en":"Science Fiction", "no":"Science Fiction"}', 'icon' => 'science_fiction.svg', 'placeholder' => 'science_fiction.jpg'],
            ['name' => '{"en":"Short", "no":"Kort"}', 'icon' => 'short.svg', 'placeholder' => 'short.jpg'],
            ['name' => '{"en":"Silent", "no":"Stille"}', 'icon' => 'silent.svg', 'placeholder' => 'silent.jpg'],
            ['name' => '{"en":"Slasher", "no":"Slasher"}', 'icon' => 'slasher.svg', 'placeholder' => 'slasher.jpg'],
            ['name' => '{"en":"Sports", "no":"Sport"}', 'icon' => 'sport.svg', 'placeholder' => 'sport.jpg'],
            ['name' => '{"en":"Spy", "no":"Spion"}', 'icon' => 'spy.svg', 'placeholder' => 'spy.jpg'],
            ['name' => '{"en":"Superhero", "no":"Superhelt"}', 'icon' => 'superhero.svg', 'placeholder' => 'superhero.jpg'],
            ['name' => '{"en":"Supernatural", "no":"Overnaturlig"}', 'icon' => 'supernatural.svg', 'placeholder' => 'supernatural.jpg'],
            ['name' => '{"en":"Suspense", "no":"Spenning"}', 'icon' => 'suspense.svg', 'placeholder' => 'suspense.jpg'],
            ['name' => '{"en":"Teen", "no":"Tenåring"}', 'icon' => 'teen.svg', 'placeholder' => 'teen.jpg'],
            ['name' => '{"en":"Thriller", "no":"Thriller"}', 'icon' => 'thriller.svg', 'placeholder' => 'thriller.jpg'],
            ['name' => '{"en":"War", "no":"Krig"}', 'icon' => 'war.svg', 'placeholder' => 'war.jpg'],
            ['name' => '{"en":"Western", "no":"Vestlig"}', 'icon' => 'western.svg', 'placeholder' => 'western.jpg'],
        ];
    }
}
