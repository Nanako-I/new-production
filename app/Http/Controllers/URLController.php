<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\People;

class URLController extends Controller
{
    
    public function sendInvitation()
    {
        $user = Auth::user();
        $people = $user->people_family()->get();
    
        $urls = [];
        foreach ($people as $person) {
            // 暗号化された people_id
            $encryptedId = Crypt::encryptString($person->id);
    
            // 暗号化したIDをルートパラメータに含める
            $urls[$person->id] = URL::temporarySignedRoute(
                'terms.show', 
                now()->addHours(24), 
                ['encrypted_id' => $encryptedId] // パラメータ名を変更
            );
        }
    
        return view('invitation', compact('urls', 'people'));
    }

public function staffsendInvitation()
{
    $url = URL::temporarySignedRoute(
        'signed.invitation_staff', 
        now()->addHours(24),
        ['signedUrl' => 'preregistrationmail']
        
    );
    return view('invitation_staff', compact( 'url'));
}

// public function handleInvalidSignature()
// {
//     abort(403, 'このURLは有効期限切れです。施設管理者に招待URLの再送を依頼してください。');
// }

public function handleInvitation(Request $request, $signedUrl)
{
    if (!$request->hasValidSignature()) {
        abort(403, '期限切れです|施設管理者に招待URLの再送を依頼してください。');
    }
    return view('terms-agreement');
}


}