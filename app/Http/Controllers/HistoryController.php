<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function data()
    {
        $merchant_history = User::join("merchants", "merchants.user_id", "=", "users.id")
            ->select("users.id as user_id", "merchants.updated_at", "merchants.created_at as date_time", "name as user_name", "is_active");

        $customer_history = User::join("customers", "customers.user_id", "=", "users.id")
            ->select("users.id as user_id", "customers.updated_at", "customers.created_at as date_time", "name as user_name", "is_active");

        $user_history_create = $merchant_history->union($customer_history)->orderBy('date_time', 'desc')->get();
        
        $data_table = collect($user_history_create)
            ->map(function ($user_history) {

                if ($user_history->updated_at == $user_history->date_time) {
                    $event_type = "New User";
                    $event_badge_color = "bg-secondary";
                } else if ($user_history->updated_at > $user_history->date_time) {
                    $event_type = "Profile Updates";
                    $event_badge_color = "bg-info";
                } else if ($user_history->is_active == 0) {
                    $event_type = "Deactivated";
                    $event_badge_color = "bg-danger";
                } else {
                    $event_type = "Active";
                    $event_badge_color = "bg-success";
                }

                if ($user_history->is_active == 0) {
                    $status = "Deactivated";
                    $badge_color = "bg-danger";
                } else {
                    $status = "Active";
                    $badge_color = "bg-success";
                }

                return [
                    "date_time" => $user_history->date_time,
                    "event_type" => '
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge ' . $event_badge_color . ' rounded-3 fw-semibold">' . $event_type . '</span>
                        </div>
                    ',
                    "user" => $user_history->user_name,
                    "status" => '
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge ' . $badge_color . ' rounded-3 fw-semibold">' . $status . '</span>
                        </div>
                    ',
                ];
            });

        return response()->json($data_table);
    }
}
