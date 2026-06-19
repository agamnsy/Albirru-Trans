<?php

namespace App\Filament\Resources\Penyewaans\Schemas;

use App\Models\Pelanggan;
use App\Models\Armada;
use App\Models\Penyewaan;
use App\Rules\NoEmoji;
use Carbon\Carbon;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Filament\Notifications\Notification;

class PenyewaanForm
{
    private static function normalizeDate($date): ?string
    {
        if (! $date) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d');
    }

    private static function normalizeTime($time): ?string
    {
        if (! $time) {
            return null;
        }

        return Carbon::parse($time)->format('H:i:s');
    }

    private static function combineDateTime($date, $time): ?string
    {
        $date = self::normalizeDate($date);
        $time = self::normalizeTime($time);

        if (! $date || ! $time) {
            return null;
        }

        return Carbon::parse("{$date} {$time}")->format('Y-m-d H:i:s');
    }

    private static function hasBentrokJadwal(
        $armadaId,
        $tanggalMulai,
        $jamMulai,
        $tanggalSelesai,
        $jamSelesai,
        $ignorePenyewaanId = null
    ): bool {
        $waktuMulai = self::combineDateTime($tanggalMulai, $jamMulai);
        $waktuSelesai = self::combineDateTime($tanggalSelesai, $jamSelesai);

        if (! $armadaId || ! $waktuMulai || ! $waktuSelesai) {
            return false;
        }

        return Penyewaan::where('armada_id', $armadaId)
            ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
            ->when($ignorePenyewaanId, function ($query) use ($ignorePenyewaanId) {
                $query->where('id', '!=', $ignorePenyewaanId);
            })
            ->whereRaw(
                "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) < ?",
                [$waktuSelesai]
            )
            ->whereRaw(
                "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) > ?",
                [$waktuMulai]
            )
            ->exists();
    }

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
                            ->modalWidth('md')
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
                                ->label('Nomor HP')
                                ->prefix('+62')
                                ->placeholder('81234567890')
                                ->helperText('Masukkan nomor tanpa angka 0 di depan.')
                                ->tel()
                                ->required()
                                ->maxLength(12)
                                ->numeric()
                                ->dehydrateStateUsing(function ($state) {
                                    if (! $state) {
                                        return null;
                                    }
                            
                                    $number = preg_replace('/[^0-9]/', '', $state);
                            
                                    if (str_starts_with($number, '08')) {
                                        return $number;
                                    }
                            
                                    if (str_starts_with($number, '62')) {
                                        return '0' . substr($number, 2);
                                    }
                            
                                    if (str_starts_with($number, '8')) {
                                        return '0' . $number;
                                    }
                            
                                    return $number;
                                })
                                ->rules([
                                    function (string $attribute, $value, \Closure $fail) {
                                        $number = preg_replace('/[^0-9]/', '', $value);
                            
                                        if (str_starts_with($number, '08')) {
                                            $normalized = $number;
                                        } elseif (str_starts_with($number, '62')) {
                                            $normalized = '0' . substr($number, 2);
                                        } elseif (str_starts_with($number, '8')) {
                                            $normalized = '0' . $number;
                                        } else {
                                            $fail('Format nomor HP tidak valid. Gunakan nomor Indonesia, contoh: 81234567890.');
                                            return;
                                        }
                            
                                        if (! preg_match('/^08[0-9]{8,11}$/', $normalized)) {
                                            $fail('Format nomor HP tidak valid. Gunakan nomor Indonesia, contoh: 81234567890.');
                                            return;
                                        }
                            
                                        $exists = Pelanggan::where('no_hp', $normalized)->exists();
                            
                                        if ($exists) {
                                            $fail('Nomor HP ini sudah terdaftar di sistem.');
                                        }
                                    },
                                ])
                                ->validationMessages([
                                    'maxLength' => 'Nomor HP tidak boleh lebih dari 12 digit setelah +62',
                                    'required' => 'Nomor HP wajib diisi',
                                    'tel' => 'Format nomor HP tidak valid',
                                ])
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

                // --- Detail Waktu Penyewaan ---
                Grid::make(2)
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
                            ->placeholder('Pilih tanggal mulai sewa')
                            ->validationMessages([
                                'required' => 'Tanggal mulai sewa wajib diisi.',
                            ])
                            ->required(),

                        TimePicker::make('jam_mulai')
                            ->label('Jam Mulai Sewa')
                            ->native(false)
                            ->seconds(false)
                            ->default('07:00')
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') : null)
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('armada_id', null);
                            })
                            ->validationMessages([
                                'required' => 'Jam mulai sewa wajib diisi.',
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
                            ->required(),

                        TimePicker::make('jam_selesai')
                            ->label('Jam Selesai Sewa')
                            ->native(false)
                            ->seconds(false)
                            ->default('17:00')
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') : null)
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('armada_id', null);
                            })
                            ->disabled(fn (Get $get) => ! $get('tanggal_selesai'))
                            ->validationMessages([
                                'required' => 'Jam selesai sewa wajib diisi.',
                            ])
                            ->required(),
                    ]),
                
                // --- Bagian Armada ---
                Select::make('armada_id')
                    ->label('Armada yang Dipesan')
                    ->columnSpanFull()
                    ->placeholder(fn (Get $get) => ! $get('tanggal_mulai') || ! $get('jam_mulai') || ! $get('tanggal_selesai') || ! $get('jam_selesai')
                        ? 'Pilih tanggal dan jam sewa terlebih dahulu'
                        : 'Pilih armada'
                    )
                    ->disabled(fn (Get $get) => ! $get('tanggal_mulai') || ! $get('jam_mulai') || ! $get('tanggal_selesai') || ! $get('jam_selesai'))
                    ->options(function (Get $get, $record) {
                        $tanggalMulai = $get('tanggal_mulai');
                        $jamMulai = $get('jam_mulai');
                        $tanggalSelesai = $get('tanggal_selesai');
                        $jamSelesai = $get('jam_selesai');

                        if (! $tanggalMulai || ! $jamMulai || ! $tanggalSelesai || ! $jamSelesai) {
                            return [];
                        }

                        $waktuMulai = self::combineDateTime($tanggalMulai, $jamMulai);
                        $waktuSelesai = self::combineDateTime($tanggalSelesai, $jamSelesai);

                        $options = Armada::where('status', 'tersedia')
                            ->whereDoesntHave('penyewaans', function ($query) use ($waktuMulai, $waktuSelesai, $record) {
                                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                                    ->when($record, function ($query) use ($record) {
                                        $query->where('id', '!=', $record->id);
                                    })
                                    ->whereRaw(
                                        "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) < ?",
                                        [$waktuSelesai]
                                    )
                                    ->whereRaw(
                                        "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) > ?",
                                        [$waktuMulai]
                                    );
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
                    ->searchPrompt('Ketik nama armada')
                    ->noOptionsMessage('Tidak ada armada yang tersedia pada waktu tersebut')
                    ->noSearchResultsMessage('Armada tidak ditemukan')
                    ->loadingMessage('Mencari armada yang tersedia...')
                    ->searchingMessage('Mencari...')
                    ->rules(function (Get $get, $record) {
                        return [
                            function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                if (! $value) {
                                    return;
                                }

                                $bentrok = self::hasBentrokJadwal(
                                    $value,
                                    $get('tanggal_mulai'),
                                    $get('jam_mulai'),
                                    $get('tanggal_selesai'),
                                    $get('jam_selesai'),
                                    $record?->id
                                );

                                if ($bentrok) {
                                    $fail('Armada ini sudah memiliki penyewaan aktif pada rentang waktu tersebut.');
                                }
                            },
                        ];
                    })
                    ->validationMessages([
                        'required' => 'Armada wajib dipilih.',
                        'exists' => 'Armada yang dipilih tidak valid.',
                    ])
                    ->required(),

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