<?php

namespace App\Filament\Resources\Penyewaans\Schemas;

use App\Models\Pelanggan;
use App\Models\Armada;
use App\Models\Penyewaan;
use App\Rules\NoEmoji;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;

class PenyewaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- Bagian Pelanggan ---
                Select::make('pelanggan_id')
                    ->label('Nama Pelanggan')
                    ->placeholder('Pilih pelanggan')
                    ->relationship('pelanggan', 'nama')
                    ->columnSpanFull()
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
                        if ($operation === 'edit') {
                            return [];
                        }

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

                // --- Detail Tanggal ---
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Sewa')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d F Y')
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('tanggal_selesai', null);
                                $set('armada_id', null);
                            })
                            // ->minDate(now())
                            ->placeholder('Pilih tanggal mulai sewa')
                            ->validationMessages([
                                'required' => 'Tanggal mulai sewa wajib diisi.',
                            ])
                            ->required(),

                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai Sewa')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d F Y')
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('armada_id', null);
                            })
                            ->disabled(fn (Get $get) => ! $get('tanggal_mulai'))
                            ->minDate(fn (Get $get) => $get('tanggal_mulai'))
                            ->placeholder(fn (Get $get) => ! $get('tanggal_mulai')
                                ? 'Pilih tanggal mulai sewa dahulu'
                                : 'Pilih tanggal selesai'
                            )
                            ->afterOrEqual('tanggal_mulai')
                            ->validationMessages([
                                'after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                                'required' => 'Tanggal selesai sewa wajib diisi.',
                            ])
                            ->rules(function (Get $get, $record) {
                                return [
                                    function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                        $armadaId = $get('armada_id');
                                        $tanggalMulai = $get('tanggal_mulai');
                                        $tanggalSelesai = $value;

                                        if (! $armadaId || ! $tanggalMulai || ! $tanggalSelesai) {
                                            return;
                                        }

                                        $exists = Penyewaan::where('armada_id', $armadaId)
                                            ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                                            ->when($record, function ($query) use ($record) {
                                                $query->where('id', '!=', $record->id);
                                            })
                                            ->whereDate('tanggal_mulai', '<=', $tanggalSelesai)
                                            ->whereDate('tanggal_selesai', '>=', $tanggalMulai)
                                            ->exists();

                                        if ($exists) {
                                            $fail('Armada ini sudah memiliki penyewaan aktif pada rentang tanggal tersebut.');
                                        }
                                    },
                                ];
                            })
                            ->required(),
                        
                        // --- Bagian Armada ---
                        Select::make('armada_id')
                        ->label('Armada yang Dipesan')
                        ->placeholder(fn (Get $get) => ! $get('tanggal_mulai') || ! $get('tanggal_selesai')
                            ? 'Pilih tanggal sewa terlebih dahulu'
                            : 'Pilih armada'
                        )
                        ->disabled(fn (Get $get) => ! $get('tanggal_mulai') || ! $get('tanggal_selesai'))
                        ->options(function (Get $get, $record) {
                            $tanggalMulai = $get('tanggal_mulai');
                            $tanggalSelesai = $get('tanggal_selesai');

                            if (! $tanggalMulai || ! $tanggalSelesai) {
                                return [];
                            }

                            $options = Armada::where('status', 'tersedia')
                                ->whereDoesntHave('penyewaans', function ($query) use ($tanggalMulai, $tanggalSelesai, $record) {
                                    $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                                        ->when($record, function ($query) use ($record) {
                                            $query->where('id', '!=', $record->id);
                                        })
                                        ->whereDate('tanggal_mulai', '<=', $tanggalSelesai)
                                        ->whereDate('tanggal_selesai', '>=', $tanggalMulai);
                                })
                                ->orderBy('nama_bus')
                                ->pluck('nama_bus', 'id');

                            if ($record && $record->armada_id) {
                                $currentArmada = Armada::withTrashed()
                                    ->where('id', $record->armada_id)
                                    ->pluck('nama_bus', 'id');

                                return $currentArmada->union($options);
                            }

                            return $options;
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        ->exists('armadas', 'id')
                        // ->helperText(function (Get $get, $operation) {
                        //     if (! $get('tanggal_mulai') || ! $get('tanggal_selesai')) {
                        //         // return 'Pilih tanggal mulai dan tanggal selesai terlebih dahulu.';
                        //     }

                        //     return $operation === 'edit'
                        //         ? 'Hanya armada yang tersedia pada rentang tanggal tersebut yang ditampilkan. Armada lama tetap ditampilkan untuk menjaga data penyewaan.'
                        //         : 'Hanya armada yang tersedia pada rentang tanggal tersebut yang ditampilkan.';
                        // })
                        ->searchPrompt('Ketik nama armada')
                        ->noOptionsMessage('Tidak ada armada yang tersedia pada tanggal tersebut')
                        ->noSearchResultsMessage('Armada tidak ditemukan')
                        ->loadingMessage('Mencari armada yang tersedia...')
                        ->searchingMessage('Mencari...')
                        ->validationMessages([
                            'required' => 'Armada wajib dipilih.',
                            'exists' => 'Armada yang dipilih tidak valid.',
                        ])
                        ->required(),
                    ]),

                // --- Detail Perjalanan ---
                Textarea::make('alamat_penjemputan')
                    ->label('Alamat Penjemputan')
                    ->rows(3)
                    ->placeholder('Masukkan alamat penjemputan pelanggan')
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->validationMessages([
                        'required' => 'Alamat penjemputan wajib diisi.',
                    ])
                    ->required(),

                Textarea::make('tujuan')
                    ->label('Tujuan Destinasi')
                    ->placeholder('Cth: Kabupaten Tegal')
                    ->rows(3)
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->validationMessages([
                        'required' => 'Tujuan destinasi wajib diisi.',
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
                            'berjalan' => 'Penyewaan sedang berjalan. Admin dapat menandai selesai jika perjalanan sudah berakhir.',
                            'selesai' => 'Status penyewaan sudah selesai dan tidak dapat diubah kembali.',
                            'dibatalkan' => 'Status penyewaan sudah dibatalkan dan tidak dapat diubah kembali.',
                            default => 'Gunakan perubahan status manual hanya untuk data lama atau kondisi khusus.',
                        };
                    })
                    ->validationMessages([
                        'required' => 'Status penyewaan wajib dipilih.',
                    ])
                    ->required(),
            ]);
    }
}