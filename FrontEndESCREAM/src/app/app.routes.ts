import { Routes } from '@angular/router';
import { CabeceraLoginComponent } from './components/cabecera-login/cabecera-login.component';
import { MainComponent } from './components/main/main.component';
import { MoviesListComponent } from './components/movies-list/movies-list.component';
import { MainLayoutComponent } from './layout/main-layout/main-layout.component';

export const routes: Routes = [
  {
    path: '',
    component: MainLayoutComponent,  // todas las rutas con header/footer
    children: [
      { path: '', component: MainComponent },
      { path: 'movies', component: MoviesListComponent },
    ]
  },
  // Rutas sin layout (login, register)
  { path: 'login', component: CabeceraLoginComponent },
];
