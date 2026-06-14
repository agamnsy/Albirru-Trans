<?php

namespace App\Filament\Resources\Penyewaans\Schemas;

use App\Models\Pelanggan;
use App\Models\Armada;
use App\Rules\NoEmoji;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;

class PenyewaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- Bagian Pelanggan ---
                Select::make('pelanggan_id')
                    ->label('Pelanggan')
                    ->placeholder('Pilih pelanggan')
                    ->relationship('pelanggan', 'nama')
                    ->searchable(['nama', 'no_hp'])
                    ->preload()
                    ->validationMessages([
                        'required' => 'Pilih atau tambah pelanggan terlebih dahulu.',
                    ])
                    ->required()
                    ->searchPrompt('Ketik nama atau nomor HP pelanggan')
                    ->noOptionsMessage('Belum ada pelanggan yang terdaftar')
                    ->noSearchResultsMessage('Pelanggan tidak ditemukan')
                    ->loadingMessage('Sedang mencari pelanggan...')
                    ->searchingMessage('Mencari...')
                    ->disabled(fn ($operation) => $operation === 'edit')
                    ->dehydrated()
                    // Modal
                    ->createOptionAction(function ($action, $operation) {
                        if ($operation === 'edit') {
                            return $action->hidden();
                        }
                        return $action
                            ->modalHeading('Tambah Pelanggan Baru')
                            ->modalSubmitActionLabel('Tambah Pelanggan')
                            ->modalCancelActionLabel('Batal');
                    })
                    ->createOptionForm(function ($operation, Schema $schema) {
                        if ($operation === 'edit') return [];
                        
                        return [
                            TextInput::make('nama')
                                ->placeholder('Masukkan nama pelanggan')
                                ->validationMessages([
                                    'required' => 'Nama pelanggan wajib diisi',
                                ])
                                ->required(),
                            TextInput::make('no_hp')
                                ->tel()
                                ->placeholder('Cth: 081234567890')
                                ->required()
                                ->maxLength(13)
                                ->numeric()
                                ->validationMessages([
                                    'maxLength' => 'Nomor HP tidak boleh lebih dari 13 digit',
                                    'unique' => 'Nomor HP ini sudah terdaftar di sistem',
                                    'required' => 'Nomor HP wajib diisi',
                                    'tel' => 'Format nomor HP tidak valid',
                                ]) 
                                ->unique('pelanggans', 'no_hp'),
                        ];
                    })
                    ->createOptionUsing(function (array $data) {
                        $pelanggan = Pelanggan::create($data);
                    
                        Notification::make()
                            ->success()
                            ->title('Pelanggan Ditambahkan')
                            ->body('Berhasil menambahkan pelanggan baru')
                            ->send();
                    
                        return $pelanggan->id;
                    }),

                // --- Bagian Armada ---
                Select::make('armada_id')
                    ->label('Armada yang Dipesan')
                    ->placeholder('Pilih armada')
                    ->relationship('armada', 'nama_bus')
                    ->searchable()
                    ->noOptionsMessage('Belum ada armada yang ditambahkan')
                    ->preload()
                    ->options(function ($record) {
                        $options = Armada::where('status', 'tersedia')
                            ->pluck('nama_bus', 'id');

                        // Pastikan saat EDIT, armada yang sedang disewa tetap muncul namanya
                        if ($record && $record->armada_id) {
                            $currentArmada = Armada::where('id', $record->armada_id)
                                ->pluck('nama_bus', 'id');
                            
                            // return $options->union($currentArmada);
                        }

                        return $options;
                    })
                    ->exists('armadas', 'id')
                    ->helperText(fn ($operation) => $operation === 'edit' 
                        ? 'Silakan pilih ulang armada jika ingin mengubah bus' 
                        : null
                    )  
                    ->searchPrompt('Ketik nama armada')
                    ->noSearchResultsMessage('Armada tidak ditemukan')
                    ->loadingMessage('Mencari armada yang tersedia...')
                    ->searchingMessage('Mencari...')
                    ->validationMessages([
                        'required' => 'Armada wajib dipilih.',
                    ])
                    ->required(),

                // --- Detail Tanggal ---
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Sewa')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d F Y')
                            ->live()
                            // ->minDate(now())
                            ->placeholder('Pilih tanggal mulai sewa')
                            ->required(),

                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai Sewa')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d F Y')
                            ->disabled(fn (Get $get) => !$get('tanggal_mulai'))
                            // ->minDate(fn (Get $get) => $get('tanggal_mulai') ?? now())
                            ->minDate(fn (Get $get) => $get('tanggal_mulai'))
                            ->placeholder(fn (Get $get) => !$get('tanggal_mulai') 
                                ? 'Pilih tanggal mulai sewa dahulu' 
                                : 'Pilih tanggal selesai'
                            )
                            ->afterOrEqual('tanggal_mulai')
                            ->validationMessages([
                                'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                                'required' => 'Tanggal selesai sewa wajib diisi.',
                            ])
                            ->required(),
                    ]),

                // --- Detail Perjalanan ---
                Textarea::make('alamat_penjemputan')
                    ->rows(3)
                    ->placeholder('Masukkan alamat penjemputan pelanggan')
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),

                Textarea::make('tujuan')
                    ->label('Tujuan Destinasi')
                    ->placeholder('Cth: Kabupaten Tegal')
                    ->rows(3)
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),

                // --- Status ---
                Select::make('status')
                    ->label('Status Penyewaan')
                    ->native(false)
                    ->columnSpanFull()
                    ->options(function ($record) {
                        if (! $record) {
                            return [
                                'pending' => 'Pending',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'berjalan' => 'Berjalan',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ];
                        }

                        return match ($record->status) {
                            'pending' => [
                                'pending' => 'Pending',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ],

                            'dikonfirmasi' => [
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ],

                            'berjalan' => [
                                'berjalan' => 'Berjalan',
                                'selesai' => 'Selesai',
                            ],

                            'selesai' => [
                                'selesai' => 'Selesai',
                            ],

                            'dibatalkan' => [
                                'dibatalkan' => 'Dibatalkan',
                            ],

                            default => [
                                'pending' => 'Pending',
                            ],
                        };
                    })
                    ->default('pending')
                    ->disabled(fn ($record) => $record && in_array($record->status, ['selesai', 'dibatalkan']))
                    ->dehydrated(fn ($record) => ! ($record && in_array($record->status, ['selesai', 'dibatalkan'])))
                    ->helperText(function ($record) {
                        if (! $record) {
                            return 'Gunakan perubahan status manual hanya untuk data lama atau kondisi khusus.';
                        }

                        return match ($record->status) {
                            'pending' => 'Penyewaan masih menunggu konfirmasi. Admin dapat membatalkan penyewaan jika pelanggan tidak jadi menyewa.',
                            'dikonfirmasi' => 'Penyewaan sudah dikonfirmasi. Admin dapat membatalkan penyewaan atau menandai selesai.',
                            'selesai' => 'Status penyewaan sudah selesai dan tidak dapat diubah kembali.',
                            'dibatalkan' => 'Status penyewaan sudah dibatalkan dan tidak dapat diubah kembali.',
                            default => 'Gunakan perubahan status manual hanya untuk data lama atau kondisi khusus.',
                        };
                    })
                    ->required(),
            ]);
    }
}
