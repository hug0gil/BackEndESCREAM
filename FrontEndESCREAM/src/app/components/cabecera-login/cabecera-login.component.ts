import { Component, EventEmitter, Output } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'cabecera-login',
  standalone: true,
  imports: [ReactiveFormsModule],
  // Usa el template que te proporcionaré en el paso 2
  templateUrl: './cabecera-login.component.html',
  styleUrl: './cabecera-login.component.css',
})
export class CabeceraLoginComponent {
  @Output() loginSucces = new EventEmitter<boolean>();
  form: FormGroup;
  submitted = false; 

  constructor() {
    this.form = new FormGroup({
      mail: new FormControl('', [Validators.required, Validators.email]),
      password: new FormControl('', [
        Validators.required,
        Validators.minLength(8),
        Validators.pattern('.*[0-9].*'),
      ]),
    });
  }

  isValid(): void {
    this.submitted = true;

    if (this.form.valid) {
      console.log('Login exitoso');
      this.loginSucces.emit(true);
    } else {
      console.log('Formulario inválido');
      // this.form.markAllAsTouched(); 
      this.loginSucces.emit(false);
    }
  }

  loginGithub() {
    console.log('Login en GitHub');
  }

  loginGoogle() {
    console.log('Login en Google');
  }
}