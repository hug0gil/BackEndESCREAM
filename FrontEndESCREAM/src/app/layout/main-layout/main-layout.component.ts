import { Component } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { MainComponent } from "../../components/main/main.component";
import { FooterComponent } from "../../components/footer/footer.component";
import { CabeceraLoginComponent } from "../../components/cabecera-login/cabecera-login.component";

@Component({
  selector: 'app-main-layout',
  standalone: true,
  imports: [HeaderComponent, FooterComponent],
  templateUrl: './main-layout.component.html',
  styleUrl: './main-layout.component.css'
})
export class MainLayoutComponent {

}
