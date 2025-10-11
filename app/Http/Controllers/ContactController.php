<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Models\CompanyInfo;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.form');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name'    => 'required|string|max:100',
                'email'   => 'required|email',
                'message' => 'required|string|max:1000',
            ],
            [
                'required' => ':attributeは必須です。',
                'email'    => ':attributeの形式が正しくありません。',
                'max'      => ':attributeは:max文字以内で入力してください。',
            ],
            [
                'name'    => 'お名前',
                'email'   => 'メールアドレス',
                'message' => 'お問い合わせ内容',
            ]
        );
        // CompanyInfoモデルを使ってメールアドレス取得
        $toEmail = CompanyInfo::where('key', 'contact-mail')->value('value');

        // メール送信
        if ($toEmail) {
            Mail::to($toEmail)->send(new ContactMail($request->all()));
        }

        return redirect()->route('contact.complete');
    }

    public function complete()
    {
        return view('contact.complete');
    }
}
