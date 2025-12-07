<?php

use App\Enums\UserTypeEnum;
use App\Models\City;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create user types
    UserType::factory()->create(['id' => UserTypeEnum::Admin->value]);
    UserType::factory()->create(['id' => UserTypeEnum::RetailCustomer->value]);
    UserType::factory()->create(['id' => UserTypeEnum::CompanyCustomer->value]);
    UserType::factory()->create(['id' => UserTypeEnum::Seller->value]);

    // Create a default city for tests
    City::factory()->create();
});

describe('viewAny', function () {
    it('allows admins to view all users', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);

        expect($admin->can('viewAny', User::class))->toBeTrue();
    });

    it('denies non-admins from viewing all users', function () {
        $customer = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $seller = User::factory()->create(['type_id' => UserTypeEnum::Seller->value]);

        expect($customer->can('viewAny', User::class))->toBeFalse();
        expect($seller->can('viewAny', User::class))->toBeFalse();
    });
});

describe('view', function () {
    it('allows admins to view any user', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($admin->can('view', $otherUser))->toBeTrue();
    });

    it('allows users to view their own profile', function () {
        $user = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($user->can('view', $user))->toBeTrue();
    });

    it('denies users from viewing other users profiles', function () {
        $user = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::Seller->value]);

        expect($user->can('view', $otherUser))->toBeFalse();
    });
});

describe('create', function () {
    it('allows admins to create users', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);

        expect($admin->can('create', User::class))->toBeTrue();
    });

    it('denies non-admins from creating users', function () {
        $customer = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $seller = User::factory()->create(['type_id' => UserTypeEnum::Seller->value]);

        expect($customer->can('create', User::class))->toBeFalse();
        expect($seller->can('create', User::class))->toBeFalse();
    });
});

describe('update', function () {
    it('allows admins to update any user', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($admin->can('update', $otherUser))->toBeTrue();
    });

    it('allows users to update their own profile', function () {
        $user = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($user->can('update', $user))->toBeTrue();
    });

    it('denies users from updating other users', function () {
        $user = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::Seller->value]);

        expect($user->can('update', $otherUser))->toBeFalse();
    });
});

describe('delete', function () {
    it('allows admins to delete other users', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($admin->can('delete', $otherUser))->toBeTrue();
    });

    it('prevents admins from deleting themselves', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);

        expect($admin->can('delete', $admin))->toBeFalse();
    });

    it('denies non-admins from deleting users', function () {
        $customer = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $seller = User::factory()->create(['type_id' => UserTypeEnum::Seller->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($customer->can('delete', $otherUser))->toBeFalse();
        expect($seller->can('delete', $otherUser))->toBeFalse();
    });
});

describe('restore', function () {
    it('allows admins to restore users', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);
        $deletedUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($admin->can('restore', $deletedUser))->toBeTrue();
    });

    it('denies non-admins from restoring users', function () {
        $customer = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $deletedUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($customer->can('restore', $deletedUser))->toBeFalse();
    });
});

describe('forceDelete', function () {
    it('allows admins to permanently delete other users', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($admin->can('forceDelete', $otherUser))->toBeTrue();
    });

    it('prevents admins from permanently deleting themselves', function () {
        $admin = User::factory()->create(['type_id' => UserTypeEnum::Admin->value]);

        expect($admin->can('forceDelete', $admin))->toBeFalse();
    });

    it('denies non-admins from permanently deleting users', function () {
        $customer = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);
        $otherUser = User::factory()->create(['type_id' => UserTypeEnum::RetailCustomer->value]);

        expect($customer->can('forceDelete', $otherUser))->toBeFalse();
    });
});
