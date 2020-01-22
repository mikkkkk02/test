<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Room;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('rooms')->delete();

        $rooms = [
        	/* Taguig */
        	[
        		'location_id' => 2,
        		'name' => 'Mission Room (Conference Room 10-1B)',
        	],
        	[
        		'location_id' => 2,
        		'name' => 'Vision Room  (Conference Room 10-1A)',
        	],
        	[
        		'location_id' => 2,
        		'name' => 'Values Room (Boardroom 11-1)',
        	],
        	[
        		'location_id' => 2,
				'name' => 'Business Room (Small Room 10-1)',
        	],
        	[
        		'location_id' => 2,
        		'name' => 'Purpose Room (Special Room 2)',
        	],
        	[
        		'location_id' => 2,
        		'name' => 'Strategy Room (Special Room 1)',
        	],

        	/* Ambuklao */
        	[
        		'location_id' => 3,
        		'name' => 'Amb - Generator Room'
	        ],
	        [
	        	'location_id' => 3,
	        	'name' => 'Amb - Transformer Room',
	        ],
	        [
	        	'location_id' => 3,
	        	'name' => 'Amb - Generator-Transformer Room',
	        ],

	        /* Binga */
	        [ 
	        	'location_id' => 5,
	        	'name' => 'Bng - Generator Room',
			],
			[ 
				'location_id' => 5,
				'name' => 'Bng - Transformer Room',
			],
			[ 
				'location_id' => 5,
				'name' => 'Bng - Generator-Transformer Room'
	        ],

	        /* Magat */
	        [
	        	'location_id' => 4,
	        	'name' => 'Magat Conference Room',
	        ],
	        [
	        	'location_id' => 4,
	        	'name' => 'Magat Small Room',
	        ],
	    ];

	    $userId = User::first()->id;

	    foreach ($rooms as $room) {
	    	$room['creator_id'] = $userId;
	    	$room['updater_id'] = $userId;
	    	Room::create($room);
	    }
    }
}
