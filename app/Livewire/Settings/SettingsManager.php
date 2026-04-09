<?php

namespace App\Livewire\Settings;

use App\Models\BankAccount;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'general';

    // WhatsApp Settings
    public $whatsapp_provider = ''; // fonnte, qontak, etc.
    public $whatsapp_token = '';
    public $whatsapp_endpoint = '';

    // WhatsApp Templates
    public $templates = [];
    public $showTemplateModal = false;
    public $editTemplateId = null;
    public $template_name = '';
    public $template_description = '';
    public $template_content = '';
    public $template_is_active = true;

    // General Settings
    public $company_name = '';
    public $company_address = '';
    public $company_phone = '';
    public $company_email = '';
    public $company_logo;
    public $existing_logo = '';
    public $timezone = 'Asia/Jakarta';

    // Map Settings
    public $map_latitude = -6.200000;
    public $map_longitude = 106.816666;
    public $map_zoom = 13;

    // Billing Settings
    public $invoice_issue_day = 1;
    public $grace_period_days = 7;
    public $subscription_type = 'postpaid';
    public $tax_percentage = 0;
    public $late_fee_percentage = 0;
    public $proration_enabled = false;
    public $proration_threshold_days = 15;

    // Payment Gateway Settings
    public $tripay_enabled = false;
    public $tripay_api_key = '';
    public $tripay_private_key = '';
    public $tripay_merchant_code = '';
    public $tripay_mode = 'sandbox';

    public $ipaymu_enabled = false;
    public $ipaymu_api_key = '';
    public $ipaymu_va = '';

    public $duitku_enabled = false;
    public $duitku_merchant_code = '';
    public $duitku_api_key = '';

    public $mayar_enabled = false;
    public $mayar_api_key = '';
    // Technical Settings
    public $pppoe_username_prefix = '';
    public $pppoe_username_suffix = '';
    public $pppoe_password_length = 6;

    // ... (existing properties)



    public function loadSettings()
    {
        // Fetch all settings at once to prevent N+1 queries causing long load delays
        $settingsQuery = Setting::all()->mapWithKeys(function ($item) {
            $value = $item->value;
            $decoded = json_decode($value, true);
            return [$item->group . '.' . $item->key => (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value];
        })->toArray();

        $getSetting = function ($key, $default) use ($settingsQuery) {
            return $settingsQuery[$key] ?? $default;
        };

        // General
        $this->company_name = $getSetting('general.company_name', '');
        $this->company_address = $getSetting('general.company_address', '');
        $this->company_phone = $getSetting('general.company_phone', '');
        $this->company_email = $getSetting('general.company_email', '');
        $this->existing_logo = $getSetting('general.company_logo', '');
        $this->timezone = $getSetting('general.timezone', 'Asia/Jakarta');
        $this->map_latitude = $getSetting('general.map_latitude', -6.200000);
        $this->map_longitude = $getSetting('general.map_longitude', 106.816666);
        $this->map_zoom = $getSetting('general.map_zoom', 13);

        // Billing
        $this->invoice_issue_day = $getSetting('billing.invoice_issue_day', 1);
        $this->grace_period_days = $getSetting('billing.grace_period_days', 7);
        $this->subscription_type = $getSetting('billing.subscription_type', 'postpaid');
        $this->tax_percentage = $getSetting('billing.tax_percentage', 0);
        $this->late_fee_percentage = $getSetting('billing.late_fee_percentage', 0);
        $this->proration_enabled = $getSetting('billing.proration_enabled', false);
        $this->proration_threshold_days = $getSetting('billing.proration_threshold_days', 15);

        // Payment Gateways
        $this->tripay_enabled = (bool) $getSetting('payment.tripay_enabled', false);
        $this->tripay_api_key = $getSetting('payment.tripay_api_key', '');
        $this->tripay_private_key = $getSetting('payment.tripay_private_key', '');
        $this->tripay_merchant_code = $getSetting('payment.tripay_merchant_code', '');
        $this->tripay_mode = $getSetting('payment.tripay_mode', 'sandbox');

        $this->ipaymu_enabled = (bool) $getSetting('payment.ipaymu_enabled', false);
        $this->ipaymu_api_key = $getSetting('payment.ipaymu_api_key', '');
        $this->ipaymu_va = $getSetting('payment.ipaymu_va', '');

        $this->duitku_enabled = (bool) $getSetting('payment.duitku_enabled', false);
        $this->duitku_merchant_code = $getSetting('payment.duitku_merchant_code', '');
        $this->duitku_api_key = $getSetting('payment.duitku_api_key', '');

        $this->mayar_enabled = (bool) $getSetting('payment.mayar_enabled', false);
        $this->mayar_api_key = $getSetting('payment.mayar_api_key', '');

        // Technical
        $this->pppoe_username_prefix = $getSetting('technical.pppoe_username_prefix', '');
        $this->pppoe_username_suffix = $getSetting('technical.pppoe_username_suffix', '');
        $this->pppoe_password_length = $getSetting('technical.pppoe_password_length', 6);

        // WhatsApp
        $this->whatsapp_provider = $getSetting('whatsapp.provider', '');
        $this->whatsapp_token = $getSetting('whatsapp.token', '');
        $this->whatsapp_endpoint = $getSetting('whatsapp.endpoint', '');
    }

    public function saveTechnical()
    {
        $this->validate([
            'pppoe_username_prefix' => 'nullable|string|max:20',
            'pppoe_username_suffix' => 'nullable|string|max:20',
            'pppoe_password_length' => 'required|integer|min:4|max:32',
        ]);

        Setting::set('technical.pppoe_username_prefix', $this->pppoe_username_prefix);
        Setting::set('technical.pppoe_username_suffix', $this->pppoe_username_suffix);
        Setting::set('technical.pppoe_password_length', $this->pppoe_password_length);

        $this->dispatch('toast', type: 'success', message: 'Pengaturan teknis berhasil disimpan.');
    }

    public function setWhatsappProvider($provider)
    {
        if ($this->whatsapp_provider === $provider) {
            $this->whatsapp_provider = null;
        } else {
            $this->whatsapp_provider = $provider;
        }
    }

    public function saveWhatsapp()
    {
        $this->validate([
            'whatsapp_provider' => 'nullable|string|in:fonnte,qontak,pushwa,watzap',
            'whatsapp_token' => 'nullable|string',
        ]);

        Setting::set('whatsapp.provider', $this->whatsapp_provider);
        Setting::set('whatsapp.token', $this->whatsapp_token);

        // If logic requires endpoint
        // Setting::set('whatsapp.endpoint', $this->whatsapp_endpoint);

        $this->dispatch('toast', type: 'success', message: 'Pengaturan WhatsApp berhasil disimpan.');
    }

    public function testWhatsapp()
    {
        $this->validate([
            'whatsapp_provider' => 'required',
            'whatsapp_token' => 'required',
            'company_phone' => 'required', // Use company phone as target
        ]);

        try {
            // Temporarily instantiate service with current form values to test without saving
            // Or just save first? Let's save first to be sure.
            $this->saveWhatsapp();

            $service = new \App\Services\Whatsapp\WhatsappService();
            $result = $service->send($this->company_phone, "Tes koneksi WhatsApp dari SKNET-CRM berhasil!");

            if ($result) {
                $this->dispatch('toast', type: 'success', message: 'Pesan tes berhasil dikirim ke ' . $this->company_phone);
            } else {
                $this->dispatch('toast', type: 'error', message: 'Gagal mengirim pesan tes. Periksa token atau koneksi.');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    // Template Methods
    public function loadTemplates()
    {
        $this->templates = \App\Models\WhatsappMessageTemplate::all()->toArray();
    }

    public function openTemplateModal()
    {
        $this->resetTemplateForm();
        $this->showTemplateModal = true;
    }

    public function editTemplate($id)
    {
        $template = \App\Models\WhatsappMessageTemplate::find($id);
        if ($template) {
            $this->editTemplateId = $id;
            $this->template_name = $template->name;
            $this->template_description = $template->description;
            $this->template_content = $template->content;
            $this->template_is_active = $template->is_active;
            $this->showTemplateModal = true;
        }
    }

    public function saveTemplate()
    {
        $this->validate([
            'template_name' => 'required|string|max:100|unique:whatsapp_message_templates,name,' . $this->editTemplateId,
            'template_content' => 'required|string',
            'template_description' => 'nullable|string|max:255',
        ]);

        // Extract variables from content {variable_name}
        preg_match_all('/\{(.*?)\}/', $this->template_content, $matches);
        $variables = isset($matches[1]) ? array_unique($matches[1]) : [];

        $data = [
            'name' => $this->template_name,
            'description' => $this->template_description,
            'content' => $this->template_content,
            'variables' => array_values($variables),
            'is_active' => $this->template_is_active,
        ];

        if ($this->editTemplateId) {
            \App\Models\WhatsappMessageTemplate::find($this->editTemplateId)->update($data);
        } else {
            \App\Models\WhatsappMessageTemplate::create($data);
        }

        $this->closeTemplateModal();
        $this->loadTemplates();
        $this->dispatch('toast', type: 'success', message: 'Template WhatsApp berhasil disimpan.');
    }

    public function deleteTemplate($id)
    {
        \App\Models\WhatsappMessageTemplate::destroy($id);
        $this->loadTemplates();
        $this->dispatch('toast', type: 'success', message: 'Template WhatsApp berhasil dihapus.');
    }

    public function resetDefaultTemplates()
    {
        $defaults = [
            [
                'name' => 'REGISTRASI',
                'description' => 'Template pesan saat pelanggan baru berhasil mendaftar',
                'content' => "Pelanggan Yth, \nKami informasikan bahwa pendaftaran langganan internet anda sudah berhasil dengan rincian sebagai berikut :\n\nNo.Layanan : {nolayanan}\nPelanggan : {nama_pelanggan}\nNo Whatsapp : {phone}\nAlamat: {alamat}\nPaket : *{profile} ({harga})*\nJenis tagihan : {jenis_tagihan}\nTgl Aktif : {tgl_aktif}\nTgl Isolir : {tgl_isolir}\n\nUntuk melihat rincian layanan dan pembayaran tagihan silahkan klik link dibawah ini {link_client_area}\n\nTerima kasih telah memilih kami sebagai penyedia layanan internet Anda. Kepuasan Anda adalah prioritas kami 🙏 \n\nApabila ada data yang tidak sesuai mohon untuk menghubungi kami.\nTerima kasih.",
                'variables' => ['nama_pelanggan', 'nolayanan', 'phone', 'alamat', 'profile', 'harga', 'jenis_tagihan', 'tgl_aktif', 'tgl_isolir', 'link_client_area']
            ],
            [
                'name' => 'PENAGIHAN',
                'description' => 'Template pesan tagihan bulanan pelanggan',
                'content' => "Pelanggan yang terhormat,\nBerikut kami sampaikan tagihan anda bulan ini :\n\nInvoice : {invoice}\nPelanggan : {nama_pelanggan}\nNo.Layanan : {nolayanan}\nProfil Internet : {profile}\n\nTotal Tagihan : *{total}*\n*Pembayaran paling lambat : {jatuh_tempo}*\n\nSilahkan klik link di bawah ini untuk melihat rincian invoice dan pembayaran :\n{link_invoice}\n\nJika anda mengalami kesulitan dalam melakukan pembayaran silahkan hubungi kami kembali.\nTerima kasih",
                'variables' => ['invoice', 'nama_pelanggan', 'nolayanan', 'profile', 'jatuh_tempo', 'total', 'link_invoice']
            ],
            [
                'name' => 'ISOLIR',
                'description' => 'Template pesan saat layanan diisolir karena tunggakan',
                'content' => "Pelanggan yang terhormat,\nKami informasikan bahwa layanan internet anda saat ini sedang di *ISOLIR* oleh sistem, kami mohon maaf atas ketidaknyamanannya\nAgar layanan internet dapat digunakan kembali dimohon untuk melakukan pembayaran tagihan sebagai berikut :\n\nInvoice : {invoice}\nPelanggan : {nama_pelanggan}\nNo.Layanan : {nolayanan}\nProfil Internet : {profile}\nTotal Tagihan : *{total}*\nTgl jatuh tempo : *{jatuh_tempo}*\n\nSilahkan klik link di bawah ini untuk melihat rincian invoice dan pembayaran :\n{link_invoice}\n\nJika anda mengalami kesulitan dalam melakukan pembayaran silahkan hubungi kami kembali.\nTerima kasih",
                'variables' => ['invoice', 'nama_pelanggan', 'nolayanan', 'profile', 'jatuh_tempo', 'total', 'link_invoice']
            ],
            [
                'name' => 'PELUNASAN',
                'description' => 'Template pesan konfirmasi pembayaran diterima',
                'content' => "Terima kasih 🙏\nPembayaran invoice sudah kami terima\n\nNo. Invoice : {invoice}\nPelanggan : {nama_pelanggan}\nNo. Layanan : {nolayanan}\nProfil Internet : {profile}\nCarabayar : {channel}\nTotal dibayarkan : {total}\nTanggal Lunas : {tgl_lunas}\n\n✅ Layanan sudah aktif dan dapat digunakan sampai dengan {tgl_isolir} \n✳️ Jika internet belum terhubung silahkan matikan dan hidupkan kembali Modem/ONT\n\nTerima kasih",
                'variables' => ['invoice', 'nama_pelanggan', 'nolayanan', 'profile', 'channel', 'tgl_lunas', 'total', 'tgl_isolir']
            ]
        ];

        foreach ($defaults as $data) {
            \App\Models\WhatsappMessageTemplate::updateOrCreate(
                ['name' => $data['name']],
                [
                    'description' => $data['description'],
                    'content' => $data['content'],
                    'variables' => $data['variables'],
                    'is_active' => true
                ]
            );
        }

        $this->loadTemplates();
        $this->dispatch('toast', type: 'success', message: 'Template default berhasil dimuat.');
    }

    public function toggleTemplateActive($id)
    {
        $template = \App\Models\WhatsappMessageTemplate::find($id);
        if ($template) {
            $template->update(['is_active' => !$template->is_active]);
            $this->loadTemplates();
        }
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
        $this->resetTemplateForm();
    }

    private function resetTemplateForm()
    {
        $this->editTemplateId = null;
        $this->template_name = '';
        $this->template_description = '';
        $this->template_content = '';
        $this->template_is_active = true;
    }

    public $bankAccounts = [];
    public $showBankModal = false;
    public $editBankId = null;
    public $bank_name = '';
    public $account_number = '';
    public $account_holder = '';
    public $branch = '';
    public $bank_is_active = true;

    // Confirmation Modal State
    public $showConfirmationModal = false;
    public $confirmationTitle = '';
    public $confirmationMessage = '';
    public $confirmationAction = '';
    public $confirmationId = null;

    protected $rules = [
        'company_name' => 'nullable|string|max:255',
        'company_address' => 'nullable|string',
        'company_phone' => 'nullable|string|max:50',
        'company_email' => 'nullable|email|max:255',
        'company_logo' => 'nullable|image|max:2048',
        'timezone' => 'required|string',
        'invoice_issue_day' => 'required|integer|min:1|max:28',
        'grace_period_days' => 'required|integer|min:0|max:30',
        'subscription_type' => 'required|in:prepaid,postpaid,both',
        'tax_percentage' => 'required|numeric|min:0|max:100',
        'late_fee_percentage' => 'required|numeric|min:0|max:100',
        'proration_enabled' => 'required|boolean',
        'proration_threshold_days' => 'required|integer|min:0|max:30',
        // Technical
        'pppoe_username_prefix' => 'nullable|string|max:20',
        'pppoe_username_suffix' => 'nullable|string|max:20',
        'pppoe_password_length' => 'required|integer|min:4|max:32',
        // Bank Accounts
        'bank_name' => 'required|string|max:100',
        'account_number' => 'required|string|max:50',
        'account_holder' => 'required|string|max:255',
        'branch' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->loadBankAccounts();
        $this->loadTemplates();

        if (request()->has('tab')) {
            $this->activeTab = request()->query('tab');
        }
    }



    public function loadBankAccounts()
    {
        $this->bankAccounts = BankAccount::ordered()->get()->toArray();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function deleteLogo()
    {
        if ($this->existing_logo) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->existing_logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->existing_logo);
            }
            Setting::set('general.company_logo', null);
            $this->existing_logo = null;
            $this->company_logo = null;
            $this->dispatch('toast', type: 'success', message: 'Logo perusahaan berhasil dihapus.');
        }
    }

    public function saveGeneral()
    {
        $this->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_logo' => 'nullable|image|max:2048',
            'timezone' => 'required|string',
            'map_latitude' => 'required|numeric|between:-90,90',
            'map_longitude' => 'required|numeric|between:-180,180',
            'map_zoom' => 'required|integer|min:1|max:20',
        ]);

        Setting::set('general.company_name', $this->company_name);
        Setting::set('general.company_address', $this->company_address);
        Setting::set('general.company_phone', $this->company_phone);
        Setting::set('general.company_email', $this->company_email);
        Setting::set('general.timezone', $this->timezone);
        Setting::set('general.map_latitude', $this->map_latitude);
        Setting::set('general.map_longitude', $this->map_longitude);
        Setting::set('general.map_zoom', $this->map_zoom);

        if ($this->company_logo) {
            $path = $this->company_logo->store('logos', 'public');
            Setting::set('general.company_logo', $path);
            $this->existing_logo = $path;
        }

        $this->dispatch('toast', type: 'success', message: 'Pengaturan umum berhasil disimpan.');
    }

    public function saveBilling()
    {
        $this->validate([
            'invoice_issue_day' => 'required|integer|min:1|max:28',
            'grace_period_days' => 'required|integer|min:0|max:30',
            'subscription_type' => 'required|in:prepaid,postpaid,both',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'late_fee_percentage' => 'required|numeric|min:0|max:100',
            'proration_enabled' => 'required|boolean',
            'proration_threshold_days' => 'required|integer|min:0|max:30',
        ]);

        Setting::set('billing.invoice_issue_day', $this->invoice_issue_day);
        Setting::set('billing.grace_period_days', $this->grace_period_days);
        Setting::set('billing.subscription_type', $this->subscription_type);
        Setting::set('billing.tax_percentage', $this->tax_percentage);
        Setting::set('billing.late_fee_percentage', $this->late_fee_percentage);
        Setting::set('billing.proration_enabled', (bool) $this->proration_enabled);
        Setting::set('billing.proration_threshold_days', $this->proration_threshold_days);

        $this->dispatch('toast', type: 'success', message: 'Pengaturan billing berhasil disimpan.');
    }

    public function savePayment()
    {
        // Tripay
        Setting::set('payment.tripay_enabled', (bool) $this->tripay_enabled);
        Setting::set('payment.tripay_api_key', $this->tripay_api_key);
        Setting::set('payment.tripay_private_key', $this->tripay_private_key);
        Setting::set('payment.tripay_merchant_code', $this->tripay_merchant_code);
        Setting::set('payment.tripay_mode', $this->tripay_mode);

        // iPaymu
        Setting::set('payment.ipaymu_enabled', (bool) $this->ipaymu_enabled);
        Setting::set('payment.ipaymu_api_key', $this->ipaymu_api_key);
        Setting::set('payment.ipaymu_va', $this->ipaymu_va);

        // Duitku
        Setting::set('payment.duitku_enabled', (bool) $this->duitku_enabled);
        Setting::set('payment.duitku_merchant_code', $this->duitku_merchant_code);
        Setting::set('payment.duitku_api_key', $this->duitku_api_key);

        // Mayar
        Setting::set('payment.mayar_enabled', (bool) $this->mayar_enabled);
        Setting::set('payment.mayar_api_key', $this->mayar_api_key);

        $this->dispatch('toast', type: 'success', message: 'Pengaturan payment gateway berhasil disimpan.');
    }

    // Bank Account Methods
    public function openBankModal()
    {
        $this->resetBankForm();
        $this->showBankModal = true;
    }

    public function editBank($id)
    {
        $bank = BankAccount::find($id);
        if ($bank) {
            $this->editBankId = $id;
            $this->bank_name = $bank->bank_name;
            $this->account_number = $bank->account_number;
            $this->account_holder = $bank->account_holder;
            $this->branch = $bank->branch;
            $this->bank_is_active = $bank->is_active;
            $this->showBankModal = true;
        }
    }

    public function saveBank()
    {
        $this->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
        ]);

        $data = [
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'account_holder' => $this->account_holder,
            'branch' => $this->branch,
            'is_active' => $this->bank_is_active,
        ];

        if ($this->editBankId) {
            BankAccount::find($this->editBankId)->update($data);
        } else {
            $data['sort_order'] = BankAccount::max('sort_order') + 1;
            BankAccount::create($data);
        }

        $this->closeBankModal();
        $this->loadBankAccounts();
        $this->dispatch('toast', type: 'success', message: 'Rekening bank berhasil disimpan.');
    }

    public function deleteBank($id)
    {
        BankAccount::destroy($id);
        $this->loadBankAccounts();
        $this->dispatch('toast', type: 'success', message: 'Rekening bank berhasil dihapus.');
    }

    public function toggleBankActive($id)
    {
        $bank = BankAccount::find($id);
        if ($bank) {
            $bank->update(['is_active' => !$bank->is_active]);
            $this->loadBankAccounts();
        }
    }

    public function closeBankModal()
    {
        $this->showBankModal = false;
        $this->resetBankForm();
    }

    private function resetBankForm()
    {
        $this->editBankId = null;
        $this->bank_name = '';
        $this->account_number = '';
        $this->account_holder = '';
        $this->branch = '';
        $this->bank_is_active = true;
    }

    public function render()
    {
        return view('livewire.settings.settings-manager')
            ->layout('layouts.app');
    }

    public function triggerConfirm($action, $id, $title, $message)
    {
        $this->confirmationAction = $action;
        $this->confirmationId = $id;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;
        $this->showConfirmationModal = true;
    }

    public function closeConfirmationModal()
    {
        $this->showConfirmationModal = false;
        $this->confirmationAction = '';
        $this->confirmationId = null;
        $this->confirmationTitle = '';
        $this->confirmationMessage = '';
    }

    public function executeAction()
    {
        if ($this->confirmationAction && method_exists($this, $this->confirmationAction)) {
            $this->{$this->confirmationAction}($this->confirmationId);
        }
        $this->closeConfirmationModal();
    }
}
