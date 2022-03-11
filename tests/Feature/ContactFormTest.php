<?php

namespace Tests\Feature;

use App\Http\Livewire\ContactForm;
use App\Mail\ContactFormMailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    /** @test */
    public function main_page_contains_contact_form_livewire_component()
    {
        $this->get('/')
            ->assertSeeLivewire('contact-form');
    }

    /** @test */
    public function contact_form_sends_out_an_email()
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Omar')
            ->set('email', 'someguy@someguy.com')
            ->set('phone', '1234567890')
            ->set('message', 'This is a test message.')
            ->call('submitForm')
            ->assertSee('We received your message successfully and will get back to you shortly');

        Mail::assertSent(function (ContactFormMailable $mail) {
            $mail->build();

            return $mail->hasTo('andre@andre.com') &&
                $mail->hasFrom('someguy@someguy.com') &&
                $mail->subject === 'Contact Form Submission';
        });
    }

    /** @test */
    public function contact_form_phone_has_min_10_characters()
    {

        Livewire::test(ContactForm::class)
            ->set('name', 'Omar')
            ->set('email', 'someguy@someguy.com')
            ->set('phone', '12345')
            ->set('message', 'This is a test message.')
            ->call('submitForm')
            ->assertHasErrors(['phone' => 'min']);

    }
}
