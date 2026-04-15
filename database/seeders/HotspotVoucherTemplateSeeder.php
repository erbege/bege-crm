<?php

namespace Database\Seeders;

use App\Models\HotspotVoucherTemplate;
use Illuminate\Database\Seeder;

class HotspotVoucherTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Thermal Printer (58mm) - Optimized for Contrast and Spacing
        HotspotVoucherTemplate::create([
            'name' => 'Thermal 58mm (Premium)',
            'content' => <<<'blade'
<style>
    @page { margin: 0; size: 58mm auto; }
    body { 
        font-family: 'Courier New', monospace; 
        font-size: 13px; 
        margin: 0; 
        padding: 8px; 
        color: #000; 
        background: #fff;
    }
    .voucher { 
        margin-bottom: 25px; 
        border-bottom: 2px dashed #000; 
        padding-bottom: 15px; 
        text-align: center; 
        page-break-inside: avoid; 
    }
    .header { font-size: 18px; font-weight: 800; margin-bottom: 2px; text-transform: uppercase; }
    .sub-header { font-size: 10px; margin-bottom: 10px; font-style: italic; }
    .code-box { 
        margin: 12px 0; 
        padding: 10px 5px; 
        border: 2px solid #000; 
        display: block;
    }
    .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
    .code { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
    .info { font-size: 11px; margin: 8px 0; line-height: 1.4; }
    .price-section { 
        border-top: 1px solid #000; 
        margin-top: 10px; 
        padding-top: 8px; 
        font-size: 16px; 
        font-weight: bold; 
    }
    .footer { font-size: 9px; margin-top: 8px; opacity: 0.8; }
</style>

@foreach($vouchers as $voucher)
    <div class="voucher">
        <div class="header">{{ config('app.name', 'SKNET') }}</div>
        <div class="sub-header">HOTSPOT ACCESS VOUCHER</div>
        
        <div class="label">{{ $voucher->profile->name }}</div>
        
        <div class="code-box">
            <div class="label">Access Code</div>
            <div class="code">{{ $voucher->code }}</div>
        </div>
        
        <div class="info">
            Validity: <strong>{{ $voucher->time_limit ?? 'Unlimited' }}</strong><br>
            Quota: <strong>{{ $voucher->data_limit ? number_format($voucher->data_limit/1048576, 0) . ' MB' : 'Unlimited' }}</strong>
        </div>

        <div class="price-section">
            <span style="font-size: 10px; vertical-align: middle;">RP</span> {{ number_format($voucher->profile->price, 0, ',', '.') }}
        </div>
        
        <div class="footer">
            Login Page: <strong>http://sknet.login</strong><br>
            Enjoy our high-speed connection!
        </div>
    </div>
@endforeach
blade
            ,
            'is_active' => true,
        ]);

        // 2. ID Card Style (Grid) - Modern Glassmorphism Look
        HotspotVoucherTemplate::create([
            'name' => 'ID Card Premium (A4)',
            'content' => <<<'blade'
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap');
    body { font-family: 'Outfit', sans-serif; margin: 0; padding: 20px; background: #f8fafc; color: #1e293b; }
    .container { display: grid; grid-template-columns: repeat(auto-fill, minmax(85mm, 1fr)); gap: 15px; justify-items: center; }
    .card {
        width: 85mm;
        height: 55mm;
        background: #fff;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        page-break-inside: avoid;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }
    .card-header {
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        color: #f1f5f9;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-header .brand { font-size: 14px; font-weight: 800; letter-spacing: 0.5px; }
    .card-header .type { font-size: 9px; text-transform: uppercase; background: #6366f1; padding: 2px 6px; border-radius: 4px; }
    
    .card-body { padding: 12px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    
    .profile-name { font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase; }
    
    .code-container {
        background: #f1f5f9;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        border: 2px dashed #cbd5e1;
    }
    .code-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; font-weight: bold; }
    .code-text { font-family: monospace; font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: 1px; }
    
    .card-footer {
        padding: 8px 12px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .footer-info { font-size: 10px; color: #64748b; }
    .footer-price { font-weight: 800; color: #0f172a; font-size: 14px; }
    
    .accents { position: absolute; bottom: -10px; left: -10px; width: 40px; height: 40px; background: #6366f1; opacity: 0.1; border-radius: 50%; }
</style>

<div class="container">
    @foreach($vouchers as $voucher)
        <div class="card">
            <div class="accents"></div>
            <div class="card-header">
                <div class="brand">{{ config('app.name', 'SKNET') }}</div>
                <div class="type">Hotspot</div>
            </div>
            <div class="card-body">
                <div class="profile-name">{{ $voucher->profile->name }}</div>
                <div class="code-container">
                    <div class="code-label">Access Code</div>
                    <div class="code-text">{{ $voucher->code }}</div>
                </div>
            </div>
            <div class="card-footer">
                <div class="footer-info">
                    {{ $voucher->time_limit ?? 'UNL' }} / {{ $voucher->data_limit ? number_format($voucher->data_limit/1048576, 0) . 'MB' : 'UNL' }}
                </div>
                <div class="footer-price">Rp {{ number_format($voucher->profile->price, 0, ',', '.') }}</div>
            </div>
        </div>
    @endforeach
</div>
blade
            ,
            'is_active' => true,
        ]);

        // 3. QR Code Style - Professional Mini Ticket
        HotspotVoucherTemplate::create([
            'name' => 'QR Code Advanced',
            'content' => <<<'blade'
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
    body { font-family: 'Inter', sans-serif; background: #fff; padding: 20px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
    .ticket {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        page-break-inside: avoid;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .ticket-top {
        background: #0f172a;
        color: white;
        padding: 10px;
        text-align: center;
    }
    .ticket-top .brand { font-weight: 900; font-size: 14px; letter-spacing: 1px; }
    .ticket-top .profile { font-size: 10px; opacity: 0.7; text-transform: uppercase; margin-top: 2px; }
    
    .ticket-main { padding: 15px; text-align: center; }
    .qr-box { 
        margin: 0 auto 10px; 
        padding: 5px; 
        border: 1px solid #f1f5f9; 
        border-radius: 8px; 
        width: 100px; 
        height: 100px; 
        display: flex; 
        align-items: center; 
        justify-content: center;
    }
    .qr-box img { max-width: 100%; height: auto; }
    
    .code-display { 
        font-size: 18px; 
        font-weight: 900; 
        color: #1e293b; 
        margin-top: 5px; 
        letter-spacing: 1px;
        font-family: monospace;
    }
    
    .ticket-footer {
        background: #f1f5f9;
        padding: 8px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px dashed #cbd5e1;
    }
    .limit-info { font-size: 9px; font-weight: bold; color: #64748b; text-align: left; }
    .price-tag { font-weight: 800; color: #ef4444; font-size: 13px; }
</style>

<div class="grid">
    @foreach($vouchers as $voucher)
        <div class="ticket">
            <div class="ticket-top">
                <div class="brand">{{ config('app.name', 'SKNET') }}</div>
                <div class="profile">{{ $voucher->profile->name }}</div>
            </div>
            <div class="ticket-main">
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://sknet.login/login?username={{ $voucher->code }}&password={{ $voucher->password }}" alt="QR">
                </div>
                <div style="font-size: 9px; color: #94a3b8; text-transform: uppercase;">Scan or Enter Code</div>
                <div class="code-display">{{ $voucher->code }}</div>
            </div>
            <div class="ticket-footer">
                <div class="limit-info">
                    {{ $voucher->time_limit ?? 'UNL' }}<br>{{ $voucher->data_limit ? number_format($voucher->data_limit/1048576) . ' MB' : 'UNLIMITED' }}
                </div>
                <div class="price-tag">Rp {{ number_format($voucher->profile->price, 0, ',', '.') }}</div>
            </div>
        </div>
    @endforeach
</div>
blade
            ,
            'is_active' => true,
        ]);

        // 4. Default Colorful - Vibrant Modern Gradient
        HotspotVoucherTemplate::create([
            'name' => 'Colorful Modern Luxe',
            'content' => <<<'blade'
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; padding: 20px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
    .luxe-card {
        background: linear-gradient(135deg, #0cebeb 0%, #20e3b2 50%, #29ffc6 100%);
        border-radius: 20px;
        padding: 20px;
        color: #064e3b;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        page-break-inside: avoid;
    }
    .luxe-card::after {
        content: '';
        position: absolute;
        top: -50%; right: -50%; width: 150%; height: 150%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }
    .brand-title { font-weight: 800; font-size: 18px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
    .badge { background: rgba(255,255,255,0.25); font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: 700; }
    
    .code-section { 
        background: rgba(255,255,255,0.9); 
        padding: 15px; 
        border-radius: 12px; 
        margin: 15px 0; 
        text-align: center;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
    .code-label { font-size: 10px; color: #065f46; text-transform: uppercase; font-weight: 800; margin-bottom: 5px; opacity: 0.6; }
    .code-value { font-size: 26px; font-weight: 800; color: #064e3b; letter-spacing: 2px; font-family: monospace; }
    
    .lower-part { display: flex; justify-content: space-between; align-items: flex-end; }
    .limit-box { font-size: 11px; font-weight: 700; background: rgba(0,0,0,0.05); padding: 5px 10px; border-radius: 8px; }
    .price-badge-luxe { font-size: 18px; font-weight: 900; }
</style>

<div class="grid">
    @foreach($vouchers as $voucher)
        <div class="luxe-card">
            <div class="brand-title">
                <span>{{ config('app.name', 'SKNET') }}</span>
                <span class="badge">VOUCHER</span>
            </div>
            <div style="font-size: 11px; font-weight: 700; opacity: 0.8; text-transform: uppercase;">{{ $voucher->profile->name }}</div>
            
            <div class="code-section">
                <div class="code-label">Wifi Access Code</div>
                <div class="code-value">{{ $voucher->code }}</div>
            </div>
            
            <div class="lower-part">
                <div class="limit-box">
                    {{ $voucher->time_limit ?? '∞' }} • {{ $voucher->data_limit ? number_format($voucher->data_limit/1048576) . 'MB' : '∞' }}
                </div>
                <div class="price-badge-luxe">
                    <span style="font-size: 11px;">Rp</span>{{ number_format($voucher->profile->price, 0, ',', '.') }}
                </div>
            </div>
        </div>
    @endforeach
</div>
blade
            ,
            'is_active' => true,
        ]);

        // 5. Modern Dark Premium
        HotspotVoucherTemplate::create([
            'name' => 'Modern Dark Premium',
            'content' => <<<'blade'
<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@400;600&display=swap');
    
    body { 
        margin: 0; 
        padding: 20px; 
        background: #f0f2f5; 
        font-family: 'Inter', sans-serif;
    }
    
    .voucher-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    
    .voucher {
        background: #1a202c;
        color: #fff;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        border: 1px solid #2d3748;
        page-break-inside: avoid;
    }
    
    .voucher::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
    }
    
    .header {
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #2d3748;
    }
    
    .brand {
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 1px;
        background: linear-gradient(to right, #818cf8, #c084fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .profile-badge {
        font-size: 10px;
        text-transform: uppercase;
        background: #2d3748;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: 600;
        color: #e2e8f0;
    }
    
    .main {
        padding: 20px;
        text-align: center;
    }
    
    .qr-container {
        margin: 0 auto 15px;
        width: 120px;
        height: 120px;
        background: #fff;
        padding: 8px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .qr-container img {
        max-width: 100%;
        height: auto;
    }
    
    .code-label {
        font-size: 10px;
        color: #a0aec0;
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
    
    .code-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
        letter-spacing: 2px;
    }
    
    .footer {
        background: #232d3f;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
    }
    
    .validity {
        color: #cbd5e0;
    }
    
    .price {
        font-weight: 700;
        color: #818cf8;
        font-size: 14px;
    }
</style>

<div class="voucher-grid">
    @foreach($vouchers as $voucher)
        <div class="voucher">
            <div class="header">
                <div class="brand">{{ config('app.name', 'SKNET') }}</div>
                <div class="profile-badge">{{ $voucher->profile->name }}</div>
            </div>
            <div class="main">
                <div class="qr-container">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://sknet.login/login?username={{ $voucher->code }}&password={{ $voucher->password }}" alt="QR">
                </div>
                <div class="code-label">Access Code</div>
                <div class="code-value">{{ $voucher->code }}</div>
                @if($voucher->user_mode == 'username_password')
                    <div style="font-size: 11px; color: #a0aec0;">Password: {{ $voucher->password }}</div>
                @endif
            </div>
            <div class="footer">
                <div class="validity">
                    @if($voucher->time_limit) {{ $voucher->time_limit }} @endif
                    @if($voucher->time_limit && $voucher->data_limit) | @endif
                    @if($voucher->data_limit) {{ number_format($voucher->data_limit / 1024 / 1024, 0) }} MB @endif
                    @if(!$voucher->time_limit && !$voucher->data_limit) Unlimited Access @endif
                </div>
                <div class="price">Rp{{ number_format($voucher->profile->price, 0, ',', '.') }}</div>
            </div>
        </div>
    @endforeach
</div>
blade
            ,
            'is_active' => true,
        ]);
    }
}
