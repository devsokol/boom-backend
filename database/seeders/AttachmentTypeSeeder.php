<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttachmentType;

class AttachmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $green = "\033[32m";
        $red = "\033[31m";
        $reset = "\033[0m";

        foreach ($this->data() as $type) {
            $exists = AttachmentType::where('name', $type['name'])->exists();

            if (! $exists) {
                AttachmentType::create($type);

                echo $green . 'Added: ' .  $type['name'] . $reset . PHP_EOL;
                continue;
            }

            echo $red . 'skipped: ' .  $type['name'] . $reset . PHP_EOL;
        }
    }

    /**
     * @return array
     */
    private function data(): array {
        return [
            ['name' => 'headshot'],
            ['name' => 'selftape'],
            ['name' => 'link'],
            ['name' => 'showreel'],
            ['name' => 'presentation'],
            ['name' => 'other'],
            ['name' => 'mt-reference-image', 'slug' => 'Reference image'],
            ['name' => 'mt-audition-script', 'slug' => 'Audition script'],
            ['name' => 'mt-video-clip', 'slug' => 'Video clip'],
            ['name' => 'mt-other', 'slug' => 'Other'],
        ];
    }
}
