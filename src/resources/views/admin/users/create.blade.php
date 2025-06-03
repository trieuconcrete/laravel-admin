@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Create User</h4>
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Name</label>
                                    <input name="full_name" type="text" placeholder="Name" value="{{ old('full_name') }}" required class="form-control p-2 border rounded">
                                    @error('full_name')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Email</label>
                                    <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required class="form-control p-2 border rounded">
                                    @error('email')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Username -->
                                <div class="mb-4">
                                    <label class="block text-gray-700">Username</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}" class="form-control p-2 border rounded @error('username') border-red-500 @enderror">
                                    @error('username')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Birthday -->
                                <div class="mb-4">
                                    <label class="block text-gray-700">Birthday</label>
                                    <input type="date" name="birthday" value="@formatDateForInput(old('birthday', $user->birthday ?? null))" class="form-control p-2 border rounded @error('birthday') border-red-500 @enderror">
                                    @error('birthday')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Phone</label>
                                    <input name="phone" type="text" placeholder="Phone" value="{{ old('phone') }}" class="form-control p-2 border rounded">
                                    @error('phone')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Password</label>
                                    <input name="password" type="password" placeholder="Password" required class="form-control p-2 border rounded">
                                    @error('password')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label class="block text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control mt-1 border p-2 rounded w-full" required>
                                </div>
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Role</label>
                                    <select name="role" required class="form-control p-2 border rounded">
                                        <option value="admin">Admin</option>
                                        <option value="manager">Manager</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-4">
                                    <label class="block text-gray-700">Status</label>
                                    <input type="checkbox" name="status" class="form-check-input" id="customSwitchsizelg" {{ old('status', '1') == '1' ? 'checked' : '' }} value="1">

                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700">Avatar</label>
                                    <input type="file" name="avatar" id="avatarInput" class="form-control mt-1 border p-2 rounded">
                                    <img id="avatarPreview" src="{{ asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Avatar Preview">
                                </div>
                                <div>
                                    <button type="submit" class="btn rounded-pill btn-secondary waves-effect">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('avatarInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
