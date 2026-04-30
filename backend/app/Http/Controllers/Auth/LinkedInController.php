<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class LinkedInController extends Controller
{
    /**
     * Redirect the user to the LinkedIn authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver('linkedin-openid');

        return $driver->stateless()->redirect();
    }

    /**
     * Obtain the user information from LinkedIn.
     *
     * @return JsonResponse
     */
    public function callback(): JsonResponse
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver('linkedin-openid');

        $linkedinUser = $driver->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $linkedinUser->getEmail()],
            [
                'name'            => $linkedinUser->getName(),
                'linkedin_id'     => $linkedinUser->getId(),
                'linkedin_avatar' => $linkedinUser->getAvatar(),
                'password'        => bcrypt(str()->random(24)),
            ]
        );

        if (!$user->hasAnyRole(['candidate', 'employer', 'admin'])) {
            $user->assignRole('candidate');
        }

        return response()->json([
            'token' => $user->createToken('linkedin')->plainTextToken,
            'user'  => $user,
        ]);
    }
}
