@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pb-12">
    <div class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <span class="text-slate-400 font-medium">Espace Personnel</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-bold italic tracking-tight uppercase text-sm">Portfolio Externe</span>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
                        Exporter le carnet de stage
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="grid grid-cols-12 gap-8">
            
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <img class="rounded-[2.5rem] object-cover w-full h-full ring-4 ring-slate-50" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0f172a&color=fff&size=128" alt="Profil">
                        <div class="absolute -bottom-2 -right-2 bg-blue-600 p-2 rounded-xl border-4 border-white">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>

                    <div class="text-center border-b border-slate-50 pb-6">
                        <h2 class="text-2xl font-black text-slate-900 leading-tight">{{ auth()->user()->name }}</h2>
                        <p class="text-blue-600 font-bold text-sm tracking-widest uppercase mt-1 italic">
                            {{ $medecin->annee ?? 'Externe 6ème Année' }}
                        </p>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-400 font-medium italic underline underline-offset-4 decoration-slate-200">Matricule</span>
                            <span class="text-slate-900 font-mono font-bold uppercase tracking-tighter">{{ $medecin->matricule ?? '2023-EXT-88' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-400 font-medium">Faculté</span>
                            <span class="text-slate-900 font-bold uppercase text-[11px] bg-slate-100 px-2 py-1 rounded">Alger / Oran</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em] mb-4 italic">Prochaine Garde</h3>
                        <p class="text-3xl font-light leading-none mb-1">Mardi 20 Jan.</p>
                        <p class="text-sm text-slate-400">Service des Urgences Médicales</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-10 uppercase font-black text-6xl">24H</div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm group hover:border-blue-200 transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition italic font-black">PT</div>
                            <span class="text-xs font-bold text-green-500 font-mono">+2 ce mois</span>
                        </div>
                        <h4 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $medecin->patients_count ?? '24' }}</h4>
                        <p class="text-slate-400 text-xs font-bold uppercase mt-1">Dossiers gérés</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm group hover:border-indigo-200 transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition italic font-black">GR</div>
                            <span class="text-xs font-bold text-slate-400 font-mono">Quota: 8/10</span>
                        </div>
                        <h4 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $medecin->gardes_count ?? '08' }}</h4>
                        <p class="text-slate-400 text-xs font-bold uppercase mt-1">Gardes Validées</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm group hover:border-emerald-200 transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition italic font-black">EV</div>
                            <span class="text-xs font-bold text-emerald-500 font-mono">Top 5%</span>
                        </div>
                        <h4 class="text-3xl font-black text-slate-900 tracking-tighter">17.5</h4>
                        <p class="text-slate-400 text-xs font-bold uppercase mt-1">Moyenne Stage</p>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 italic tracking-tight">Affectation Hospitalière</h3>
                                <p class="text-slate-400 text-sm italic font-medium">Rotation en cours : Trimestre 1</p>
                            </div>
                            <div class="text-right">
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">En cours</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Service de Santé</label>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center font-bold text-slate-400 italic italic">CH</div>
                                        <p class="text-slate-900 font-bold uppercase text-sm tracking-tight">{{ $medecin->service ?? 'Chirurgie Viscérale' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Responsable Pédagogique</label>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center font-bold text-slate-400 italic italic">Pr</div>
                                        <p class="text-slate-900 font-bold text-sm tracking-tight">Pr. {{ $medecin->maitre ?? 'AIT MANSOUR. K' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 rounded-3xl p-6 border border-dashed border-slate-200">
                                <h4 class="text-xs font-black text-slate-500 uppercase mb-4">Validation des Compétences</h4>
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase mb-1">
                                            <span>Pratique Clinique</span>
                                            <span>85%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-blue-600 h-full rounded-full" style="width: 85%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase mb-1">
                                            <span>Théorie & Examens</span>
                                            <span>60%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-slate-900 h-full rounded-full" style="width: 60%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection