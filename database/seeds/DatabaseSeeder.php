<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $faker = Faker::create();

        foreach (range(1,100) as $index){
        DB::table('visitors')->insert([
                'first_name' => $faker->firstName,
                'last_name' =>$faker->lastName,                
                'email' => $faker->email,
                'phone' => $faker->e164PhoneNumber,          
                'sex' => $faker->randomElement(array('female','male')),                 
                'image' => $faker->randomElement(array('a.jpg','b.jpg','c.jpg','d.jpg','e.jpg')),
                'dob' => $faker->date('Y-m-d','now'),                
                'created_at' => $faker->date('Y-m-d','now'),
                'updated_at' => $faker->date('Y-m-d','now'),
               
        ]);
    }


      foreach (range(1,5) as $index){
        DB::table('users')->insert([                
                'name' =>$faker->lastName.' '.$faker->lastName,                
                'email' => $faker->email,
                'phone' => $faker->e164PhoneNumber,
                'type' => $faker->randomElement(array('0','1')),               
                'sex' => $faker->randomElement(array('female','male')),                 
                'image' => $faker->randomElement(array('a.jpg','b.jpg','c.jpg','d.jpg','e.jpg')),                
                'password' => bcrypt('test'),
                'dob' => $faker->date('Y-m-d','now'),                
                'created_at' => $faker->date('Y-m-d','now'),
                'updated_at' => $faker->date('Y-m-d','now'),
               
        ]);
    }


           /* foreach (range(1,20) as $index){
        DB::table('users')->insert([
                'name' => $faker->name,
                'sex' => $faker->randomElement(array('male','female')),
                'account_number' => $faker->randomNumber(7),
                'phone' => $faker->e164PhoneNumber,
                'email' => $faker->email,
                'bank_name' => "Diamond",
                'alias' => $faker->firstName,
                'password' => bcrypt('test'),
                'created_at' => $faker->date('Y-m-d','now')
        ]);
    }*/


       /* $num = 4;
          foreach (range(1,30) as $index) {
            DB::table('provide_help_web')->insert([
                'user_id' => $num + 1,
                'package_type_id' => $faker->randomElement(array('1','2','3')),
                'created_at' => $faker->date('Y-m-d','now')
            ]);
       
        }
*/
        /* foreach (range(1,6000) as $index) {
            DB::table('visiting_histories')->insert([
                'hmo_staff_id' => $faker->numberBetween(1, 1000),
                'hmo_id' => $faker->numberBetween(1, 25),
                'created_at' => $faker->dateTimeThisYear($max = 'now'),
                'comment'=>$faker->sentence($nbWords = 10, $variableNbWords = true)
            ]);

        }*/
       /* foreach (range(1,10) as $index){
            DB::table('get_helpers')->insert([
                'phone' => $faker->e164PhoneNumber,
                'name' => $faker->lastName,
                'amount_to_pay' => 5,
                'created_at' => Date("Y-m-d H:i:s"),
                'updated_at' => Date("Y-m-d H:i:s")
            ]);

        }
*/
       /* foreach (range(1000,1005) as $index) {
            DB::table('hmo_staffs')->insert([
                'lname' => $faker->lastName,
                'fname' => $faker->firstName,
                'phone' => $faker->e164PhoneNumber,
                'email' => $faker->email,
                'id_card' => $faker->ean8,
                'address'=>$faker->streetAddress,
                'dob' => $faker->date('Y-m-d','now'),
                'hmo_id' =>$faker->numberBetween(0, 25),
                'image' =>$faker->image($dir="http://localhost/hmo/public/assets/staff/img", $width=640, $height=480, 'people', false)
            ]);
       
        }*/


    }
}
