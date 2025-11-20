@extends('layouts.admin')

@section('title', 'Export Edilen Dosyalar')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Export Edilen Dosyalar</h1>

        @if($files->isEmpty())
            <div class="text-gray-500">Henüz export edilmiş dosya bulunmuyor.</div>
        @else
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2 border">Dosya Adı</th>
                        <th class="px-4 py-2 border">Oluşturulma</th>
                        <th class="px-4 py-2 border">İndir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                        <tr>
                            <td class="px-4 py-2 border">{{ $file['name'] }}</td>
                            <td class="px-4 py-2 border">{{ $file['created'] }}</td>
                            <td class="px-4 py-2 border">
                                <a href="{{ $file['url'] }}" class="text-blue-600 underline" target="_blank">
                                    İndir
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection