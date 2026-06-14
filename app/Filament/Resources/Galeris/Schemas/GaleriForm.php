<?php

namespace App\Filament\Resources\Galeris\Schemas;

use App\Models\Kategori;
use App\Rules\NoEmoji;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GaleriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('media')
                    ->label('Foto / Video (Maks. upload 1 file)')
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->reorderable()
                    // ->multiple()
                    // ->panelLayout('grid')
                    ->appendFiles(false)
                    ->disk('public')
                    ->directory('galeri')
                    ->acceptedFileTypes([
                        'image/*',
                        'video/mp4',
                        'video/webm',
                        'video/quicktime',
                    ])
                    ->maxSize(10240)
                    ->helperText('Maksimal ukuran file 10MB. Format yang didukung: JPG, PNG, MP4, WEBM, MOV.')
                    ->required()
                    ->validationMessages([
                        'max' => 'Foto / Video tidak boleh lebih dari 1 file.',
                        'required' => 'Mohon unggah foto / video terlebih dahulu.',
                        'maxSize' => 'Ukuran file tidak boleh lebih dari 10MB.',
                    ]),
                Select::make('kategori_id')
                    ->label('Kategori')
                    ->placeholder('Pilih kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->noSearchResultsMessage('Kategori tidak ditemukan.')
                    ->validationMessages([
                        'required' => 'Pilih atau tambah kategori terlebih dahulu.',
                    ])
                    
                    // CREATE OPTION
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Kategori')
                            ->placeholder('Masukkan nama kategori')
                            ->validationMessages([
                                'required' => 'Nama kategori wajib diisi',
                            ])
                            ->rules([
                                new NoEmoji(),
                            ])
                            ->required(),
                        Select::make('warna')
                            ->label('Warna Kategori')
                            ->options([
                                'primary' => 'Biru',
                                'success' => 'Hijau',
                                'danger' => 'Merah',
                                'warning' => 'Kuning',
                                'gray' => 'Abu-abu',
                            ])
                            ->native(false)
                            ->default('primary')
                            ->validationMessages([
                                'required' => 'Pilih warna kategori terlebih dahulu',
                            ])
                            ->required(),

                    ])

                    // MODAL
                    ->createOptionModalHeading('Tambah Kategori')

                    // CREATE BUTTON
                    ->createOptionAction(function ($action) {

                        return $action
                            ->modalSubmitActionLabel('Tambah Kategori')
                            ->modalCancelActionLabel('Batal')
                            ->modalFooterActionsAlignment('end');
                    }),
                TextInput::make('judul')
                    ->label('Judul Galeri')
                    ->placeholder('Masukkan judul galeri')
                    ->validationMessages([
                        'required' => 'Judul galeri wajib diisi',
                    ])
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),
                TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->placeholder('Masukkan nama pelanggan')
                    ->validationMessages([
                        'required' => 'Nama pelanggan wajib diisi',
                    ])
                    ->required(),
                DatePicker::make('tanggal_penyewaan')
                    ->label('Tanggal Penyewaan')
                    ->placeholder('Masukkan tanggal')
                    ->date()
                    ->displayFormat('d F Y')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->validationMessages([
                        'required' => 'Tanggal penyewaan wajib diisi',
                    ])
                    ->required(),
                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Masukkan deskripsi tambahan')
                    ->rows(3)
                    ->validationMessages([
                        'required' => 'Deskripsi wajib diisi',
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
