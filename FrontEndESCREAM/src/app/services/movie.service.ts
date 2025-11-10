import { inject, Injectable } from '@angular/core';
import {
  BehaviorSubject,
  Observable,
  catchError,
  map,
  tap,
  throwError,
} from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Movie } from '../interfaces/movie-interface';

@Injectable({
  providedIn: 'root',
})
export class MovieService {
  // Estado interno
  private movies: Movie[] = [];

  // Subject reactivo con valor inicial vacío
  private moviesSubject = new BehaviorSubject<Movie[]>([]);
  movies$: Observable<Movie[]> = this.moviesSubject.asObservable();

  // Inyección del HttpClient con la nueva API de Angular
  private http = inject(HttpClient);

  private readonly apiUrl = 'http://localhost:8000/api/movies?per_page=25';

  constructor() {
    this.loadMovies();
  }

  /**
   * Carga las películas desde el backend al inicializar el servicio.
   * Usa tap() para efectos secundarios y catchError() para control de errores.
   */
  private loadMovies(): void {
    console.log('Load movies');
    this.http
      .get<{ data: Movie[] }>(this.apiUrl)
      .pipe(
        map((response) => response.data),
        tap((movies) => {
          this.movies = movies;
          this.moviesSubject.next([...this.movies]);
          console.log('Películas cargadas:', this.movies);
        }),
        catchError((error) => {
          console.error('Error al cargar películas:', error);
          this.moviesSubject.next([]);
          return throwError(() => error);
        })
      )
      .subscribe();
  }

  /**
   * Devuelve un observable con todas las películas.
   */
  getMovies$(): Observable<Movie[]> {
    console.log('Devuelve un observable con todas las películas');
    return this.movies$;
  }

  /**
   * Filtra películas por texto y actualiza el observable reactivo.
   */
  filterMovies(text: string): void {
    if (!text) {
      this.moviesSubject.next([...this.movies]);
      return;
    }

    const filtered = this.movies.filter(
      (m) =>
        m.title.toLowerCase().includes(text.toLowerCase()) ||
        m.synopsis.toLowerCase().includes(text.toLowerCase())
    );

    this.moviesSubject.next(filtered);
  }

  /**
   * Obtiene una película por ID desde la caché (sin petición HTTP).
   */
  getMovieById(id: number): Movie | undefined {
    return this.movies.find((m) => m.id === id);
  }
}
