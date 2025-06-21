<?php

namespace App\Http\Requests;

use App\Traits\ValidatesMediaPaths;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    use ValidatesMediaPaths;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id', null);
        $hasContract = $this->filled('contract_type_id');

        // $this->merge([
        //     'avatar' => json_decode($this->input('avatar'), true) ?? [],
        // ]);

        return [
            'code' => ['nullable', 'string', 'max:50', "unique:employees,code,{$id}"],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => [$id ? 'nullable' : 'required', 'string', 'max:255', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
            'phone' => ['nullable', 'string', 'regex:/^0\d{9}$/', "unique:employees,phone,{$id}"], // hoặc đổi theo format bạn muốn
            'address' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date_format:d-m-Y', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'cccd' => ['nullable', 'string', 'digits_between:9,12', "unique:employees,cccd,{$id}"],
            'cccd_issued_date' => ['nullable', 'date_format:d-m-Y', 'before_or_equal:today'],
            'university_start_date' => ['nullable', 'date_format:d-m-Y'],
            'university_end_date' => ['nullable', 'date_format:d-m-Y', 'after_or_equal:university_start_date'],
            'position_id' => ['required', 'exists:positions,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'education_level_id' => ['required', 'exists:education_levels,id'],

            'contract_type_id' => ['nullable', 'exists:contract_types,id'],
            'file_url'         => $id && $hasContract ? ['nullable'] : ['required', 'file', 'mimes:pdf', 'max:10240'],
            'start_date'       => $hasContract ? ['required', 'date_format:d-m-Y'] : ['nullable'],
            'end_date'         => $hasContract ? ['required', 'date_format:d-m-Y', 'after_or_equal:start_date'] : ['nullable'],
            'salary'           => $hasContract ? ['required', 'numeric', 'min:0'] : ['nullable'],

            'resignation_date' => ['nullable', 'date', 'after_or_equal:birthday'],
            'employment_status_id' => ['required', 'exists:employment_statuses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'url'],
            // 'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'code' => 'Mã nhân viên',
            'full_name' => 'Họ tên',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'birthday' => 'Ngày sinh',
            'gender' => 'Giới tính',
            'cccd' => 'Căn cước công dân',
            'cccd_issued_date' => 'Ngày cấp CCCD',
            'university_start_date' => 'Ngày nhập học',
            'university_end_date' => 'Ngày tốt nghiệp',
            'position_id' => 'Vị trí',
            'department_id' => 'Phòng ban',
            'education_level_id' => 'Trình độ học vấn',
            'resignation_date' => 'Ngày nghỉ việc',
            'employment_status_id' => 'Tình trạng làm việc',
            'notes' => 'Ghi chú',
            'avatar' => 'Ảnh đại diện',
        ];
    }

    public function passedValidation()
    {
        $this->validateMultipleMediaFields([
            'avatar' => [$this->input('avatar', [])],
            // 'galleries' => json_decode($this->input('galleries', '[]'), true),
        ]);
    }
}
