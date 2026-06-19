@extends('layouts.app')

@section('content')
<div class="container admin-page">
    <div class="admin-layout">

        <!-- Nav commune -->
        <aside>
            <div class="admin-nav">
                <div class="admin-nav-header">
                    <h3>🛡 Administration</h3>
                    <span>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</span>
                </div>
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Statistiques</a></li>
                    <li><a href="{{ route('admin.entreprises') }}" class="{{ request()->routeIs('admin.entreprises') ? 'active' : '' }}"><i class="fas fa-building"></i> Entreprises</a></li>
                    <li><a href="{{ route('admin.utilisateurs') }}" class="{{ request()->routeIs('admin.utilisateurs') ? 'active' : '' }}"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="{{ route('admin.offres') }}" class="{{ request()->routeIs('admin.offres') ? 'active' : '' }}"><i class="fas fa-briefcase"></i> Offres</a></li>
                    <li><a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories') ? 'active' : '' }}"><i class="fas fa-tags"></i> Catégories</a></li>
                    <li><a href="{{ route('admin.competances') }}" class="{{ request()->routeIs('admin.competances') ? 'active' : '' }}"><i class="fas fa-star"></i> Compétences</a></li>
                    <li><a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Messages</a></li>
                </ul>
            </div>
        </aside>

        <!-- Contenu spécifique à chaque page -->
        <div>
            @yield('admin-content')
        </div>

    </div>
</div>
@endsection
