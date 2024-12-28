<?php

namespace App\Livewire;

use App\Models\Auth\User;
use App\Repository\UserRepositoryInterface;
use App\Services\AuthServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;


class AccountSetup extends Component
{
    use WithFileUploads;

    public int $formStep;
    public Collection|null|array $interests;
    public Collection|null|array $interests_selected;
    public Collection|null|array $related;

    public string $name;
    public Collection|null $countries;
    #[Validate('required|integer')]
    public int|string $country_id;
    #[Validate('required')]
    public string|null $dial_code;

    #[Validate('required|string')]
    public string|null $phone;
    #[Validate('required')]
    public $gender;
    #[Validate('date|required')]
    public string|null $birth_date;

    #[Validate('nullable|sometimes|image')]
    public $photo;
    public $oldPhoto;

    protected $listeners = ['toggleFollow' => '$refresh'];


    public function mount(UserRepositoryInterface $userRepository){
        $this->interests_selected = [];
        $this->formStep = 1;
//        $this->interests = DB::table('interests')->get();

        $user = $userRepository->getAccountGeneralInfo();
        $this->name = $user['name'];
        $this->country_id = $user['country_id'] ?? '';
        $this->dial_code = $user['dial_code'];
        $this->phone = $user['phone'];
        $this->birth_date = $user['birth_date'];

        $this->oldPhoto = $user['avatar'] ?? null;
        if($user['gender'] === 0 || $user['gender'] === 1){
            $this->gender = $user['gender'];
        }else{ $this->gender = ''; }



        $this->countries = DB::table('countries')
            ->get(['id', 'name', 'dial_code']);
    }

    public function updateDialCode(){
        $country_id = $this->country_id;

        if($country_id){
            $found = $this->countries->filter(function($rec)use($country_id){
                return $rec->id == $country_id;
            })->first();
            if($found){
                $this->dial_code = $found->dial_code;
            }
        }else{$this->dial_code = '';}
    }

    public function step1Submit(AuthServiceInterface $authService){
        $data = ($this->validate());

        $authService->updateGeneralInfo($data);

        // Load step two
        $this->interests = DB::table('interests')->get();
        $this->formStep = 2;
    }

    public function stepBack(){
        $this->formStep--;
    }

    public function step2Submit(UserRepositoryInterface $userRepository){
        $interests = ($this->interests_selected) ?? [];
        $userRepository->updateUserInterests($interests);

        // Load Step 3 Data
        $this->related = $userRepository->getTopAccounts(20);
        $this->formStep = 3;
    }

    public function toggleFollow(int $user_id): void
    {
        $idx = $this->related->search(function($rec)use($user_id){
            return $rec->user_id == $user_id;
        });

        if($idx !== false){
            // unfollow

            $target = User::find($this->related[$idx]->user_id);

            if($this->related[$idx]->followed){
                $this->related[$idx]->followed = 0;
            }else{
                $this->related[$idx]->followed = 1;
            }
            auth()->user()->toggleFollow($target);

            $this->dispatch('toggleFollow');
        }
    }

    public function step3Submit(UserRepositoryInterface $userRepository){
        $userRepository->markAccountAsUpdated();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.account-setup')
            ->layout('layouts.auth');
    }

    /**
     * @throws ValidationException
     */
    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }
}
