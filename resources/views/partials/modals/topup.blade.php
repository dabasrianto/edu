<div id="topup-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 animate-scale-up">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-lg text-gray-900">Top Up Saldo</h3>
            <button onclick="toggleModal('topup-modal')" class="text-gray-400 hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('topup.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
           @csrf
           
           <!-- Error Alert -->
           @if ($errors->any())
               <div class="bg-red-50 text-red-600 p-3 rounded-lg text-xs">
                   <ul class="list-disc list-inside">
                       @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                       @endforeach
                   </ul>
                   @if($errors->has('proof_image')) 
                        <p class="mt-1 font-bold">Pastikan ukuran file di bawah 2MB.</p>
                   @endif
               </div>
           @endif

           <div>
               <label class="text-xs font-bold text-gray-700 block mb-2">Pilih Nominal <span class="text-red-500">*</span></label>
               <div class="grid grid-cols-2 gap-2">
                   @foreach([50000, 100000, 200000, 500000] as $val)
                   <label class="cursor-pointer relative">
                       <input type="radio" name="amount" value="{{ $val }}" class="peer sr-only" {{ old('amount') == $val ? 'checked' : '' }}>
                       <div class="text-center p-3 border rounded-lg peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 text-sm font-medium transition-all hover:bg-gray-50">
                           {{ number_format($val, 0, ',', '.') }}
                       </div>
                   </label>
                   @endforeach
               </div>
               @error('amount') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
           </div>

           <div>
               <label class="text-xs font-bold text-gray-700 block mb-2">Metode Transfer <span class="text-red-500">*</span></label>
               <select name="bank_account_id" class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 text-xs focus:outline-none focus:border-blue-500">
                    @if(isset($bankAccounts) && count($bankAccounts) > 0)
                        @foreach($bankAccounts as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_account_id') == $bank->id ? 'selected' : '' }}>
                                {{ $bank->bank_name }} - {{ $bank->account_number }} (a.n {{ $bank->account_holder }})
                            </option>
                        @endforeach
                    @else
                        <option disabled selected>Belum ada rekening tersedia</option>
                    @endif
               </select>
               @error('bank_account_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
           </div>
           
           <div>
                <label class="text-xs font-bold text-gray-700 block mb-2">Upload Bukti Transfer (Wajib) <span class="text-red-500">*</span></label>
                <input type="file" name="proof_image" required accept="image/*" class="w-full text-xs border border-gray-300 rounded-lg p-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                <p class="text-[10px] text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                @error('proof_image') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
           </div>

           <button type="submit" class="w-full bg-blue-900 text-white font-bold py-3 rounded-xl hover:bg-blue-800 shadow-lg active:scale-95 transform transition-all">
               Konfirmasi Top Up
           </button>
        </form>
    </div>
</div>
