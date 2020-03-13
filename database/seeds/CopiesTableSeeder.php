<?php

use Illuminate\Database\Seeder;

class CopiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $copies = [
            'es' => [
                'icon_tooltip' => '¿Necesitas ayuda?',
                'widget_title' => 'Ayuda',
                'widget_welcome' => 'Hola, soy el asistente virtual de Goteo. ¿Puedo ayudarte?',
                'start_icon' => 'Comenzar',
                'close_icon' => 'Cerrar',
                'go_back_icon' => 'Atrás',
                'reset_icon' => 'Reiniciar',
                'faq_title' => 'Preguntas frecuentes',
                'faq_url' => 'https://www.goteo.org/faq',
                'error_title' => '¡Uops...!',
                'error_message' => 'No he podido recuperar la información de ayuda.',
                'error_retry' => 'Reintentar',
                'loading' => 'Cargando...',
            ],
            'ca' => [
                'icon_tooltip' => 'Necessites ajuda?',
                'widget_title' => 'Ajuda',
                'widget_welcome' => 'Hola, sóc l\'assistent virtual de Goteo. Puc ajudar-te?',
                'start_icon' => 'Comença',
                'close_icon' => 'Tanca',
                'go_back_icon' => 'Enrere',
                'reset_icon' => 'Reinicia',
                'faq_title' => 'Preguntes freqüents',
                'faq_url' => 'https://ca.goteo.org/faq',
                'error_title' => 'Uops...!',
                'error_message' => 'No he pogut recuperar la informació d\'ajuda.',
                'error_retry' => 'Torna-ho a provar',
                'loading' => 'Carregant...',
            ],
            'en' => [
                'icon_tooltip' => 'Need help?',
                'widget_title' => 'Help',
                'widget_welcome' => 'Hi, I\'m the Goteo\'s virtual assistant. Can I help you?',
                'start_icon' => 'Start',
                'close_icon' => 'Close',
                'go_back_icon' => 'Back',
                'reset_icon' => 'Restart',
                'faq_title' => 'FAQs',
                'faq_url' => 'https://en.goteo.org/faq',
                'error_title' => 'Ooops...!',
                'error_message' => 'I couldn\'t retrieve this help information.',
                'error_retry' => 'Retry',
                'loading' => 'Loading...',
            ],
        ];

        foreach($copies as $lang_iso_code => $data) {
            $language = DB::table('languages')->where('iso_code', $lang_iso_code)->first();

            foreach($data as $name => $value) {
                DB::table('copies')->insert([
                    'name' => $name,
                    'value' => $value,
                    'language_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
