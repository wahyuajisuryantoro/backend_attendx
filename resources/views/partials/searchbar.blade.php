<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header border-bottom">
              <input type="search" class="form-control" placeholder="Search here" id="search" />
              <a href="javascript:void(0)" data-bs-dismiss="modal" class="lh-1">
                  <i class="ti ti-x fs-5 ms-3"></i>
              </a>
          </div>
          <div class="modal-body message-body" data-simplebar="">
              <h5 class="mb-0 fs-5 p-1">Quick Page Links</h5>
              <ul class="list mb-0 py-2" id="pageLinks">
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('dashboard') }}">
                          <span class="text-dark fw-semibold d-block">Dashboard</span>
                          <span class="fs-2 d-block text-body-secondary">/dashboard</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('employee.list') }}">
                          <span class="text-dark fw-semibold d-block">Employee List</span>
                          <span class="fs-2 d-block text-body-secondary">/employee-list</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('attendance.report.index') }}">
                          <span class="text-dark fw-semibold d-block">Attendance Report</span>
                          <span class="fs-2 d-block text-body-secondary">/attendance-report</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('leave-request.index') }}">
                          <span class="text-dark fw-semibold d-block">Leave Requests</span>
                          <span class="fs-2 d-block text-body-secondary">/leave-request</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('office.index') }}">
                          <span class="text-dark fw-semibold d-block">Office</span>
                          <span class="fs-2 d-block text-body-secondary">/office</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('work-shifts.index') }}">
                          <span class="text-dark fw-semibold d-block">Work Shifts</span>
                          <span class="fs-2 d-block text-body-secondary">/work-shifts</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('shift-assignments.index') }}">
                          <span class="text-dark fw-semibold d-block">Shift Assignments</span>
                          <span class="fs-2 d-block text-body-secondary">/shift-assignments</span>
                      </a>
                  </li>
                  <li class="p-1 mb-1 bg-hover-light-black rounded px-2 search-item">
                      <a href="{{ route('account-settings.index') }}">
                          <span class="text-dark fw-semibold d-block">Account Settings</span>
                          <span class="fs-2 d-block text-body-secondary">/account-settings</span>
                      </a>
                  </li>
              </ul>
          </div>
      </div>
  </div>
</div>
