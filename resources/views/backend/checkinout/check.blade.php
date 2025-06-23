 <!-- Modal -->

 <div class="modal fade" id="checkModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
     <div class="modal-dialog ">
         <div class="modal-content">
            {{-- modal-dialog-centered --}}
             <div class="modal-header">
                 <h5 class="modal-title">Chấm công hôm nay</h5>
                 <p id="clock" class="mb-0 text-end fw-bold text-primary"></p>
             </div>

             <div class="modal-body text-center">
                 @php
                     $checkedIn = $todayAttendance && $todayAttendance->check_in;
                     $checkedOut = $todayAttendance && $todayAttendance->check_out;
                 @endphp

                 <p id="status" class="fw-bold">
                     @if ($checkedIn)
                         ✅ Đã check in lúc: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i:s') }}
                     @else
                         Vui lòng Check In.
                     @endif
                 </p>

                 <button class="btn btn-success mx-4 {{ $checkedIn ? 'disabled' : '' }}" id="checkInBtn"
                     {{ $checkedIn ? 'disabled' : '' }}>
                     ✅ Check in
                 </button>

                 <button
                     class="btn btn-danger mx-4 {{ $checkedIn ? '' : 'd-none' }} {{ $checkedOut ? 'disabled' : '' }}"
                     id="checkOutBtn" {{ $checkedOut ? 'disabled' : '' }}>
                     ⏱ Check out
                 </button>

                 <p id="checkout" class="mb-0 mt-3 fw-bold {{ $checkedOut ? '' : 'd-none' }}">
                     @if ($checkedOut)
                         ⏱ Đã check out lúc: {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i:s') }}
                     @endif
                 </p>
             </div>

             <div class="modal-footer">
                <button class="btn btn-secondary" id="closeModalBtn" {{ $checkedIn ? '' : 'disabled' }}>Đóng</button>

             </div>

         </div>
     </div>
 </div>

 @push('scripts')
     <script>
         $(document).ready(function() {
             const $checkInBtn = $('#checkInBtn');
             const $checkOutBtn = $('#checkOutBtn');
             const $closeModalBtn = $('#closeModalBtn');
             const $statusText = $('#status');
             const $modalEl = $('#checkModal');
             const $checkout = $('#checkout');
             const modal = new bootstrap.Modal($modalEl[0]);

             let isCheckedIn = {{ $checkedIn ? 'true' : 'false' }};
             let isCheckedOut = {{ $checkedOut ? 'true' : 'false' }};
            //  const modal = new bootstrap.Modal($modalEl[0]);

             $modalEl.on('hide.bs.modal', function(e) {
                 if (!isCheckedIn) {
                     e.preventDefault();
                 }
             });


             $checkInBtn.on('click', function() {
                 if (isCheckedIn) return;
                 const time = new Date().toLocaleTimeString();
                 $.ajax({
                     url: 'attendance/checkin', // Route Laravel
                     method: 'POST',
                     data: {
                         _token: '{{ csrf_token() }}',
                         check_in_time: time
                     },
                     success: function(response) {
                         isCheckedIn = true;

                         $statusText.text(`✅ Đã check in lúc: ${time}`);
                         $checkInBtn.prop('disabled', true);
                         $checkOutBtn.removeClass('d-none');
                         $closeModalBtn.prop('disabled', false);
                     },
                     error: function(xhr) {
                         alert('Lỗi khi check in!');
                         console.error(xhr.responseText);
                     }
                 });
             });


             $checkOutBtn.on('click', function() {
                 if (!isCheckedIn || isCheckedOut) return;
                 $checkout.removeClass('d-none');
                 isCheckedOut = true;
                 const time = new Date().toLocaleTimeString();
                 $checkout.text(`\n⏱ Đã check out lúc: ${time}`);
                 $checkOutBtn.prop('disabled', true);
             });


             $closeModalBtn.on('click', function() {
                 if (isCheckedIn) {
                     modal.hide();
                 }
             });

             modal.show();

             function updateClock() {
                 const now = new Date();
                 const time = now.toLocaleTimeString();
                 $('#clock').text(time);
             }
             setInterval(updateClock, 1000);
             updateClock();
         });
     </script>
 @endpush
