<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Facility;
use App\Models\Person;
use App\Models\Role;
use App\Models\Permission;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Enums\RoleType;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Enums\PermissionType;
use App\Enums\RoleType as RoleEnums;
use App\Enums\Role as RoleEnum;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BrotherInvitationController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $facility = $user->facility_staffs()->first();
        $firstFacility = $facility;

        if ($firstFacility) {
            $facilitypeople = $firstFacility->people_facilities()
                ->with(['people_family.registered_people' => function ($query) {
                    $query->select('people.*');
                }])
                ->get();
        } else {
            $facilitypeople = collect();
        }

        $person = $facilitypeople->first();

        return view('brother-invitation', compact('person', 'facility', 'facilitypeople'));
    }

    public function register(Request $request)
    {
        // \Log::info('Request data: ', $request->all());

        $validatedData = $request->validate([
            'person_id' => 'required|integer|exists:people,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        try {
            DB::table('people_families')->insert([
                'person_id' => $validatedData['person_id'],
                'user_id' => $validatedData['user_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

             // 二重送信防止
            $request->session()->regenerateToken();
            return redirect()
            ->route('brother.invitation')
            ->with('success', 'ご家族との紐づけが完了しました');

        } catch (\Exception $e) {
            // \Log::error('Error linking user and family: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    //   public function show()
    //   {
    //       $user = Auth::user();

    //       $facility = $user->facility_staffs()->first();
    //       $firstFacility = $facility;

    //       $people = $user->people_family()->get();

    //       // Retrieve people associated with the first facility
    //       if ($firstFacility) {
    //           $facilitypeople = $firstFacility->people_facilities()->get();
    //       } else {
    //           $facilitypeople = collect(); // Use an empty collection if no people are registered
    //       }

    //       // Get the first person from the facility people collection
    //       $person = $facilitypeople->first();

    //       if ($person) {
    //           // Generate URL using the current person's ID
    //           $encryptedId = Crypt::encryptString($person->id);
    //           $url = URL::temporarySignedRoute(
    //               'terms.show', 
    //               now()->addHours(24), 
    //               ['encrypted_id' => $encryptedId]
    //           );

    //           \Log::info('Generated URL: ' . $url);
    //           \Log::info('Person ID: ' . $person->id);
    //           \Log::info('People family count: ' . $people->count());

    //           $qrCode = QrCode::size(200)->generate($url);
    //       } else {
    //           $url = null;
    //           $qrCode = null;
    //           \Log::info('No person found in the facility');
    //       }

    //       // Add this line to check if $facilitypeople has data
    //       \Log::info('Facility people count: ' . $facilitypeople->count());

    //       return view('brother-invitation', compact('url', 'qrCode', 'person', 'facility', 'facilitypeople'));
    //   }

    // public function generateUrls(Request $request)
    // {
    //     $ids = $request->input('ids');
    //     $urls = [];

    //     foreach ($ids as $id) {
    //         $encryptedId = Crypt::encryptString($id);
    //         $url = URL::temporarySignedRoute(
    //             'terms.show', 
    //             now()->addHours(24), 
    //             ['encrypted_id' => $encryptedId]
    //         );
    //         $urls[] = $url;
    //     }

    //     return response()->json(['urls' => $urls]);
    // }

    //   public function generateUrl(Request $request)
    //     {
    //         try {
    //             $ids = $request->input('ids');

    //             if (empty($ids)) {
    //                 return response()->json(['error' => 'No IDs provided'], 400);
    //             }

    //             // 複数のIDを連結して暗号化
    //             $encryptedIds = Crypt::encryptString(implode(',', $ids));

    //             $url = URL::temporarySignedRoute(
    //                 'terms.show', 
    //                 now()->addHours(24), 
    //                 ['encrypted_ids' => $encryptedIds]
    //             );

    //             return response()->json(['url' => $url]);
    //         } catch (\Exception $e) {
    //             Log::error('URL generation error: ' . $e->getMessage());
    //             return response()->json(['error' => 'An error occurred while generating the URL: ' . $e->getMessage()], 500);
    //         }
    //     }
    // public function generateUrl(Request $request)
    //   {
    //       try {
    //           $ids = $request->input('ids');

    //           if (empty($ids)) {
    //               return response()->json(['error' => 'No IDs provided'], 400);
    //           }

    //           // 複数のIDを連結して暗号化
    //           $encryptedIds = Crypt::encryptString(implode(',', $ids));

    //           $url = URL::temporarySignedRoute(
    //               'terms.show', 
    //               now()->addHours(24), 
    //               ['encrypted_ids' => $encryptedIds]
    //           );

    //           return response()->json(['url' => $url]);
    //       } catch (\Exception $e) {
    //           \Log::error('URL generation error: ' . $e->getMessage());
    //           return response()->json(['error' => 'An error occurred while generating the URL: ' . $e->getMessage()], 500);
    //       }
    //   }

    // public function show()
    // {
    //     $user = Auth::user();
    //     $facility = $user->facility_staffs()->first();

    //     $firstFacility = $facility->first();

    //     // Retrieve people associated with the first facility
    //     if ($firstFacility) {
    //         $facilitypeople = $firstFacility->people_facilities()->get();
    //     } else {
    //         $facilitypeople = collect(); // Use an empty collection if no people are registered
    //     }

    //     return view('brother-invitation', compact('facility', 'facilitypeople'));
    // }

    // public function generateUrls(Request $request)
    // {
    //     $selectedIds = $request->input('selected_ids', []);
    //     $urls = [];

    //     foreach ($selectedIds as $id) {
    //         $person = Person::findOrFail($id);
    //         $encryptedId = Crypt::encryptString($person->id);
    //         $url = URL::temporarySignedRoute(
    //             'terms.show', 
    //             now()->addHours(24), 
    //             ['encrypted_id' => $encryptedId]
    //         );
    //         $urls[$id] = $url;

    //         // Generate QR code for each URL
    //         $qrCode = QrCode::size(200)->generate($url);
    //         $urls[$id] = [
    //             'url' => $url,
    //             'qr_code' => $qrCode->toHtml(),
    //         ];
    //     }

    //     return response()->json(['urls' => $urls]);
    // }
}