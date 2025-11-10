import { Routes } from '@angular/router';
import { CabeceraLoginComponent } from './components/cabecera-login/cabecera-login.component';
import { MainComponent } from './components/main/main.component';
import { MoviesListComponent } from './components/movies-list/movies-list.component';

export const routes: Routes = [
  { path: '', component: MainComponent },
  { path: 'login', component: CabeceraLoginComponent },
  { path: 'movies', component: MoviesListComponent },
];
