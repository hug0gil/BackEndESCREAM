import { Component, inject } from '@angular/core';
import { MovieService } from '../../services/movie.service';
import { Movie } from '../../interfaces/movie-interface';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-movies-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './movies-list.component.html',
  styleUrls: ['./movies-list.component.css'],
})
export class MoviesListComponent {
  private service = inject(MovieService);
  public movies: Movie[] = [];

  ngOnInit() {
    this.service.getMovies$().subscribe((movieSub) => (this.movies = movieSub));
  }
}