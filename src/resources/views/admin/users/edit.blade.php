@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Edit User</h4>
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Name</label>
                                    <input name="full_name" type="text" placeholder="Name" value="{{ old('full_name', $user->full_name) }}" required class="form-control p-2 border rounded">
                                    @error('full_name')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Email</label>
                                    <input name="email" type="email" placeholder="Email" value="{{ old('email', $user->email) }}" required class="form-control p-2 border rounded">
                                    @error('email')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Birthday -->
                                <div class="mb-4">
                                    <label class="block text-gray-700">Birthday</label>
                                    <input type="date" name="birthday" value="{{ old('birthday', $user->birthday ?? '') }}" class="form-control p-2 border rounded @error('birthday') border-red-500 @enderror">
                                    @error('birthday')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Phone</label>
                                    <input name="phone" type="text" placeholder="Phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control p-2 border rounded">
                                    @error('phone')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Password</label>
                                    <input name="password" type="password" placeholder="Password" class="form-control p-2 border rounded">
                                    @error('password')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label class="block text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control mt-1 border p-2 rounded w-full">
                                </div>
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Role</label>
                                    <select name="role" required class="form-control p-2 border rounded">
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-4">
                                    <label class="flex items-center gap-2">
                                        <span class="text-gray-700">Status</span>
                                        <input type="checkbox" name="status" class="form-check-input" id="customSwitchsizelg" {{ old('status', default: $user->status) == 1 ? 'checked' : '' }} value="1">
                                    </label>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700">Avatar</label>
                                    <input type="file" name="avatar" id="avatarInput" class="form-control mt-1 border p-2 rounded">
                                    <img id="avatarPreview" src="{{ (isset($user) && $user->avatar) ? asset('storage/' . $user->avatar) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Avatar Preview">
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
