<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    @php($updateAccountUrlTemplate = route('admin.users.update-account', ['user' => '__USER__']))

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4">
                <h2 id="user-form-title" class="text-lg font-semibold text-slate-900">Create User</h2>
                <p class="text-sm text-slate-500">Admin can register users and assign roles. Click Edit from the table to update here.</p>
            </div>

            <form
                id="user-form"
                method="POST"
                action="{{ route('admin.users.store') }}"
                data-create-url="{{ route('admin.users.store') }}"
                data-update-url-template="{{ $updateAccountUrlTemplate }}"
                class="grid gap-4 md:grid-cols-2"
            >
                @csrf
                <input id="user-form-method" type="hidden" name="_method" value="PATCH" disabled>

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                    <input id="user-form-name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input id="user-form-email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input id="user-form-password" name="password" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <p class="mt-1 text-xs text-slate-500">Leave blank while editing if you do not want to change the password.</p>
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
                    <input id="user-form-password-confirmation" name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label for="role" class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                    <select id="user-form-role" name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', 'staff') === $role)>{{ strtoupper($role) }}</option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <button id="user-form-submit" type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Create User</button>
                    <button id="user-form-cancel" type="button" class="hidden rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel Edit</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-900">Manage Users</h2>
                <p class="text-sm text-slate-500">Only Admin can change username, password, and roles.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2 font-medium">Name</th>
                            <th class="px-3 py-2 font-medium">Email</th>
                            <th class="px-3 py-2 font-medium">Current Role</th>
                            <th class="px-3 py-2 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $user->email }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium uppercase text-slate-700">{{ $user->role }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <button
                                        type="button"
                                        class="js-edit-user rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-role="{{ $user->role }}"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-slate-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>

    <script>
        const userForm = document.getElementById('user-form');
        const userFormMethod = document.getElementById('user-form-method');
        const userFormTitle = document.getElementById('user-form-title');
        const userFormSubmit = document.getElementById('user-form-submit');
        const userFormCancel = document.getElementById('user-form-cancel');
        const userFormName = document.getElementById('user-form-name');
        const userFormEmail = document.getElementById('user-form-email');
        const userFormPassword = document.getElementById('user-form-password');
        const userFormPasswordConfirmation = document.getElementById('user-form-password-confirmation');
        const userFormRole = document.getElementById('user-form-role');

        function setCreateMode() {
            userForm.action = userForm.dataset.createUrl;
            userFormMethod.disabled = true;
            userFormTitle.textContent = 'Create User';
            userFormSubmit.textContent = 'Create User';
            userFormCancel.classList.add('hidden');

            userFormName.value = '';
            userFormEmail.value = '';
            userFormPassword.value = '';
            userFormPasswordConfirmation.value = '';
            userFormRole.value = 'staff';
        }

        function setEditMode(button) {
            const userId = button.dataset.userId;
            const updateUrl = userForm.dataset.updateUrlTemplate.replace('__USER__', userId);

            userForm.action = updateUrl;
            userFormMethod.disabled = false;
            userFormTitle.textContent = 'Edit User';
            userFormSubmit.textContent = 'Update User';
            userFormCancel.classList.remove('hidden');

            userFormName.value = button.dataset.userName;
            userFormEmail.value = button.dataset.userEmail;
            userFormPassword.value = '';
            userFormPasswordConfirmation.value = '';
            userFormRole.value = button.dataset.userRole;

            window.scrollTo({ top: userForm.offsetTop - 120, behavior: 'smooth' });
        }

        document.querySelectorAll('.js-edit-user').forEach((button) => {
            button.addEventListener('click', () => setEditMode(button));
        });

        userFormCancel.addEventListener('click', setCreateMode);
    </script>
</x-app-layout>
