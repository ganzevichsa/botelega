<?php


namespace App\Conversations;

use App\Country;
use App\HeadingSpecialist;
use App\SectionsSpecialist;
use App\Specialist;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Conversations\Conversation;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use Illuminate\Foundation\Inspiring;

class mainConversation extends conversation
{
    public $response = [];

    public function run () {
        $question = BotManQuestion::create("Привет! Выбери раздел");
        $question->addButtons( [
            Button::create('Найти специалиста')->value('1'),
            Button::create('Добавить специалиста')->value('4'),
            Button::create('Найти компанию')->value('2'),
            Button::create('Добавить компанию')->value('3'),
            Button::create('Рассказать анекдот на английском')->value('joke'),
        ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'joke') {
                    $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random'));
                    $this->say($joke->value->joke);
                }
                if ($answer->getValue() === '1'){
                    $this->specialSearch();
                }
                if ($answer->getValue() === '4'){
                    $this->specialAddCountry();
                }
                if ($answer->getValue() === '2'){
                    $message = OutgoingMessage::create('Сори этот раздел в разработке');
                    $this->bot->reply($message);
                }
                if ($answer->getValue() === '3'){
                    $message = OutgoingMessage::create('Сори этот раздел в разработке');
                    $this->bot->reply($message);
                }
            }
        });
    }


// ПОИСК Специалиста
    // Выбор страны специалиста
    private function specialSearch () {
        $question = BotManQuestion::create("Выбери из какой страны?");

        $country = Country::all();
        foreach($country as $countryItem){
            $question->addButtons([
                Button::create($countryItem->name)->value($countryItem->id),
            ]);
        }


        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialCountry();
        });
    }
    // Выбор города специалиста
    private function specialCountry () {
        $question = BotManQuestion::create("Выбери из какого города?");

        $city = Country::find($this->response[0])->cities;
        foreach($city as $cityItem){
            $question->addButtons([
                Button::create($cityItem->name)->value($cityItem->id),
            ]);
        }

        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialSearchHeading();
        });
    }
    // Выбор рубрики специалиста
    private function specialSearchHeading () {
        $question = BotManQuestion::create("Выбери рубрику?");

        $heading = HeadingSpecialist::all();
        foreach($heading as $headingItem){
            $question->addButtons([
                Button::create($headingItem->name)->value($headingItem->id),
            ]);
        }

        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialSearchSection();
        });
    }
    // Выбор раздела специалиста
    private function specialSearchSection () {
        $question = BotManQuestion::create("Выбери раздел?");

        $sections = SectionsSpecialist::where('heading_id', $this->response[2])->get();
        foreach($sections as $sectionsItem){
            $question->addButtons([
                Button::create($sectionsItem->name)->value($sectionsItem->id),
            ]);
        }

        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialSearchLimit();
        });
    }
    //Количество записей
    private function specialSearchLimit() {
        $question = BotManQuestion::create("Сколько вывести записей");

        $this->ask( $question, function ( BotManAnswer $answer ) {
            if( $answer->getText () != '' ){
                array_push ($this->response, $answer->getText());

                $this->exitSeachSpecialist ();
            }
        });
    }
    //Выбор из базы специалистов
    private function exitSeachSpecialist() {
        $tasksQuery = Specialist::query()->where('country_id',$this->response[0])->where('city_id',$this->response[1])->where('heading_id',$this->response[2])->where('sections_id',$this->response[3])->limit($this->response[4])->get();

        foreach ($tasksQuery as $tasksQueryItem)
        {
            $message = OutgoingMessage::create($tasksQueryItem->id . "." . $tasksQueryItem->name . $tasksQueryItem->number . "---");
            $this->bot->reply($message);
        }

        return true;
    }

// Конец поиска специалиста

// Добавить специалиста
    // Выбор страны специалиста
    private function specialAddCountry () {
        $question = BotManQuestion::create("Выбери из какой ты страны?");
        $country = Country::all();
        foreach($country as $countryItem){
            $question->addButtons([
                Button::create($countryItem->name)->value($countryItem->id),
            ]);
        }


        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialAddCity();
        });
    }
    // Выбор города специалиста
    private function specialAddCity () {
        $question = BotManQuestion::create("Выбери из какой ты страны?");

        $city = Country::find($this->response[0])->cities;
        foreach($city as $cityItem){
            $question->addButtons([
                Button::create($cityItem->name)->value($cityItem->id),
            ]);
        }

        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialAddHeading();
        });
    }
    // Выбор рубрики
    private function specialAddHeading () {
        $question = BotManQuestion::create("Выбери рубрику?");

        $heading = HeadingSpecialist::all();
        foreach($heading as $headingItem){
            $question->addButtons([
                Button::create($headingItem->name)->value($headingItem->id),
            ]);
        }

        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialAddSection();
        });
    }
    // Выбор раздела
    private function specialAddSection () {
        $question = BotManQuestion::create("Выбери раздел?");

        $sections = SectionsSpecialist::where('heading_id', $this->response[2])->get();
        foreach($sections as $sectionsItem){
            $question->addButtons([
                Button::create($sectionsItem->name)->value($sectionsItem->id),
            ]);
        }

        $this->ask($question, function (BotManAnswer $answer) {
            // здесь можно указать какие либо условия, но нам это не нужно сейчас

            array_push ($this->response, $answer);

            $this->specialAddName();
        });
    }
    //Добавление ФИО
    private function specialAddName() {
        $question = BotManQuestion::create("Нипиши Фамили Имя Отчество! Например: Иванов Иван Иванович");

        $this->ask( $question, function ( BotManAnswer $answer ) {
            if( $answer->getText () != '' ){
                array_push ($this->response, $answer->getText());

                $this->specialAddNumber ();
            }
        });
    }
    //Добавление номера
    private function specialAddNumber() {
        $question = BotManQuestion::create("Нипиши полный номер телефона! Например: +38067 000 00 00");

        $this->ask( $question, function ( BotManAnswer $answer ) {
            if( $answer->getText () != '' ){
                array_push ($this->response, $answer->getText());

                $this->exitSpecialADD ();
            }
        });
    }
    //Добавления в базу
    private function exitSpecialADD () {
        $db = new Specialist;
        $db->id_chat    = $this->bot->getUser()->getId();
        $db->id_name    = $this->bot->getUser()->getUsername();
        $db->country_id = $this->response[0];
        $db->city_id    = $this->response[1];
        $db->heading_id   = $this->response[2];
        $db->sections_id   = $this->response[3];
        $db->name   = $this->response[4];
        $db->number  = $this->response[5];
        $db->save();

        $message = OutgoingMessage::create('Ты добавлен в базу. Теперь тебя как специалиста смогут найти через меня! До новых встреч!'. "-" .  "-" . $this->response[4] . "-" . $this->response[5]);
        $this->bot->reply($message);

        return true;
    }


}