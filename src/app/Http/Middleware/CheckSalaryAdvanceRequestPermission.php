<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalaryAdvanceRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CheckSalaryAdvanceRequestPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Kiểm tra quyền tạo request với status approved hoặc paid
        if ($request->isMethod('POST') && $request->has('status')) {
            $status = $request->input('status');
            
            if (in_array($status, [
                SalaryAdvanceRequest::STATUS_APPROVED, 
                SalaryAdvanceRequest::STATUS_PAID
            ])) {
                if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền tạo yêu cầu với trạng thái này'
                    ], 403);
                }
            }
        }

        // Kiểm tra quyền update request với status approved hoặc paid
        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $status = $request->input('status');
            
            if (isset($status) && in_array($status, [
                SalaryAdvanceRequest::STATUS_APPROVED, 
                SalaryAdvanceRequest::STATUS_PAID
            ])) {
                if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền thay đổi trạng thái thành trạng thái này'
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
