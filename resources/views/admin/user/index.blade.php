<x-admin-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Pengguna</h1>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Layout Grid untuk pengelompokan user type -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Tabel Admin - baris penuh -->
            <div class="bg-blue-600 shadow-md rounded-lg col-span-1">
                <h2 class="text-white text-2xl font-semibold mb-4 px-6 py-4 bg-blue-600 rounded-t-lg">Admin</h2>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full bg-white rounded-lg">
                        <thead>
                            <tr class="bg-blue-600 text-white uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Nama</th>
                                <th class="py-3 px-6 text-left">Email</th>
                                <th class="py-3 px-6 text-left">Departemen</th>
                                <th class="py-3 px-6 text-left">Unit Kerja</th>
                                <th class="py-3 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm font-light">
                            @foreach ($admins as $user)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $user->name }}</td>
                                    <td class="py-3 px-6 text-left">{{ $user->email }}</td>
                                    <td class="py-3 px-6 text-left">{{ $user->departemen }}</td>
                                    <td class="py-3 px-6 text-left">{{ $user->unit_work }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <button onclick="openEditForm('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->usertype }}', '{{ $user->departemen }}', '{{ $user->unit_work }}', '{{ $user->seksi }}', '{{ $user->jabatan }}')" class="bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-700">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

          <!-- Grid untuk PKM dan Approval - dua kolom --> 
<div class="grid grid-cols-2 gap-6">
    <!-- Tabel PKM -->
    <div class="bg-orange-600 shadow-md rounded-lg">
        <h2 class="text-white text-lg font-semibold mb-2 px-4 py-3 bg-orange-600 rounded-t-lg">PKM</h2> <!-- Mengurangi padding dan ukuran font -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white rounded-lg">
                <thead>
                    <tr class="bg-orange-600 text-white uppercase text-xs leading-normal"> <!-- Ubah ukuran font -->
                        <th class="py-2 px-2 text-left">Nama</th>
                        <th class="py-2 px-2 text-left">Email</th>
                        <th class="py-2 px-2 text-left">Seksi</th>
                        <th class="py-2 px-2 text-left">Jabatan</th>
                        <th class="py-2 px-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-xs font-light"> <!-- Ubah ukuran font -->
                    @foreach ($pkms as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-1 px-2 text-left">{{ $user->name }}</td>
                            <td class="py-1 px-2 text-left">{{ $user->email }}</td>
                            <td class="py-1 px-2 text-left">{{ $user->seksi }}</td>
                            <td class="py-1 px-2 text-left">{{ $user->jabatan }}</td>
                            <td class="py-1 px-2 text-center">
                                <button onclick="openEditForm('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->usertype }}', '{{ $user->departemen }}', '{{ $user->unit_work }}', '{{ $user->seksi }}', '{{ $user->jabatan }}')" class="bg-blue-500 text-white text-xs px-2 py-1 rounded-md hover:bg-blue-700">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white text-xs px-2 py-1 rounded-md hover:bg-red-700">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Tabel Approval -->
    <div class="bg-blue-400 shadow-md rounded-lg">
        <h2 class="text-white text-lg font-semibold mb-2 px-4 py-3 bg-blue-400 rounded-t-lg">Approval</h2> <!-- Mengurangi padding dan ukuran font -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white rounded-lg">
                <thead>
                    <tr class="bg-blue-400 text-white uppercase text-xs leading-normal"> <!-- Ubah ukuran font -->
                        <th class="py-2 px-2 text-left">Nama</th>
                        <th class="py-2 px-2 text-left">Email</th>
                        <th class="py-2 px-2 text-left">Departemen</th>
                        <th class="py-2 px-2 text-left">Jabatan</th>
                        <th class="py-2 px-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-xs font-light"> <!-- Ubah ukuran font -->
                    @foreach ($approvals as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-1 px-2 text-left">{{ $user->name }}</td>
                            <td class="py-1 px-2 text-left">{{ $user->email }}</td>
                            <td class="py-1 px-2 text-left">{{ $user->departemen }}</td>
                            <td class="py-1 px-2 text-left">{{ $user->jabatan }}</td>
                            <td class="py-1 px-2 text-center">
                                <button onclick="openEditForm('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->usertype }}', '{{ $user->departemen }}', '{{ $user->unit_work }}', '{{ $user->seksi }}', '{{ $user->jabatan }}', '{{ $user->whatsapp_number }}')" class="bg-blue-500 text-white text-xs px-2 py-1 rounded-md hover:bg-blue-700">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white text-xs px-2 py-1 rounded-md hover:bg-red-700">
                                        <i class="fas fa-trash-alt"></i> 
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

            <!-- Tabel User - baris penuh -->
            <div class="bg-gray-400 shadow-md rounded-lg col-span-1">
                <h2 class="text-white text-2xl font-semibold mb-4 px-6 py-4 bg-gray-400 rounded-t-lg">User</h2>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full bg-white rounded-lg">
                        <thead>
                            <tr class="bg-gray-400 text-white uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Nama</th>
                                <th class="py-3 px-6 text-left">Email</th>
                                <th class="py-2 px-2 text-left">Departemen</th>
                                <th class="py-2 px-2 text-left">Unit Kerja</th>
                                <th class="py-2 px-2 text-left">Seksi</th>
                                <th class="py-2 px-2 text-left">Jabatan</th>
                                <th class="py-3 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm font-light">
                            @foreach ($users as $user)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $user->name }}</td>
                                    <td class="py-3 px-6 text-left">{{ $user->email }}</td>
                                    <td class="py-1 px-2 text-left">{{ $user->departemen }}</td>
                                    <td class="py-1 px-2 text-left">{{ $user->unit_work }}</td>
                                    <td class="py-1 px-2 text-left">{{ $user->seksi }}</td>
                                    <td class="py-1 px-2 text-left">{{ $user->jabatan }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <button onclick="openEditForm('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->usertype }}', '{{ $user->departemen }}', '{{ $user->unit_work }}', '{{ $user->seksi }}', '{{ $user->jabatan }}')" class="bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-700">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<!-- Modal Form untuk Edit User -->
<div id="editForm" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 text-center">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-blue-900 opacity-75"></div> <!-- Background biru dengan opacity -->
        </div>
        <div class="inline-block bg-blue-800 text-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-md w-full"> <!-- Modal warna biru -->
            <div class="bg-blue-700 px-6 py-4">
                <div class="text-center sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">Edit Pengguna</h3>
                    <div class="mt-4">
                        <form id="editUserForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="editName" class="block text-sm font-medium text-gray-300">Nama</label>
                                <input type="text" id="editName" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            </div>
                            <div class="mb-4">
                                <label for="editEmail" class="block text-sm font-medium text-gray-300">Email</label>
                                <input type="email" id="editEmail" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            </div>
                            <!-- Kolom WhatsApp Number pada form -->
                            <div class="mb-4">
                                <label for="editWhatsAppNumber" class="block text-sm font-medium text-gray-300">WhatsApp Number</label>
                                <input type="text" id="editWhatsAppNumber" name="whatsapp_number" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="editUsertype" class="block text-sm font-medium text-gray-300">Usertype</label>
                                <select id="editUsertype" name="usertype" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                    <option value="admin">Admin</option>
                                    <option value="pkm">PKM</option>
                                    <option value="approval">Approval</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="editDepartemen" class="block text-sm font-medium text-gray-300">Departemen</label>
                                <input type="text" id="editDepartemen" name="departemen" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <!-- <select id="editDepartemen" name="departemen" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="Dept. of Raw Material & Cement Production">Dept. of Raw Material & Cement Production</option>
                                    <option value="Dept. of Clinker & Cement Production">Dept. of Clinker & Cement Production</option>
                                    <option value="Dept. of Maintenance">Dept. of Maintenance</option>
                                    <option value="Dept. of Mining & Power Plant">Dept. of Mining & Power Plant</option>
                                    <option value="Dept. of Production Planning & Control">Dept. of Production Planning & Control</option>
                                    <option value="Dept. of Cement Production">Dept. of Cement Production</option>
                                    <option value="Dept. of Project Management & Main Support">Dept. of Project Management & Main Support</option>
                                </select> -->
                            </div>
                            <div class="mb-4">
                                <label for="editUnitWork" class="block text-sm font-medium text-gray-300">Unit Kerja</label>
                                <input type="text" id="editUnitWork" name="unit_work" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="editSeksi" class="block text-sm font-medium text-gray-300">Seksi</label>
                                <input type="text" id="editSeksi" name="seksi" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="editJabatan" class="block text-sm font-medium text-gray-300">Jabatan</label>
                                <select id="editJabatan" name="jabatan" class="mt-1 block w-full px-3 py-2 border border-gray-500 rounded-md shadow-sm bg-blue-900 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="Operation Directorate">Operation Directorate</option>
                                    <option value="General Manager">General Manager</option>
                                    <option value="Senior Manager">Senior Manager</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Karyawan">Karyawan</option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" onclick="closeEditForm()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function openEditForm(id, name, email, usertype, departemen, unit_work, seksi, jabatan, whatsapp_number) {
    document.getElementById('editUserForm').action = `/admin/users/${id}`;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editUsertype').value = usertype;
    document.getElementById('editDepartemen').value = departemen;
    document.getElementById('editUnitWork').value = unit_work;
    document.getElementById('editSeksi').value = seksi;
    document.getElementById('editJabatan').value = jabatan;
    document.getElementById('editWhatsAppNumber').value = whatsapp_number; 
    document.getElementById('editForm').classList.remove('hidden');
}
function closeEditForm() {
    document.getElementById('editForm').classList.add('hidden');
}
</script>
@if (session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
@endif
</x-admin-layout>
