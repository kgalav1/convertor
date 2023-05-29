import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { RouterModule } from '@angular/router';
import { AppRoutingModule } from './app.routing';
import { HttpClientModule } from '@angular/common/http';
import { AppComponent } from './app.component';
import { SignupComponent } from './signup/signup.component';
import { LandingComponent } from './landing/landing.component';
import { ProfileComponent } from './profile/profile.component';
import { HomeComponent } from './home/home.component';
import { NavbarComponent } from './shared/navbar/navbar.component';
import { FooterComponent } from './shared/footer/footer.component';

import { HomeModule } from './home/home.module';
import { AboutusComponent } from './aboutus/aboutus.component';
import { UnzipfileComponent } from './unzipfile/unzipfile.component';
// import { DragDropFileComponent } from './drag-drop-file/drag-drop-file.component';

// import { UploadsfileComponent } from './uploadsfile/uploadsfile.component';
// import { LoginComponent } from './login/login.component';
// import { LoginModule } from './login/login.module';

@NgModule({
  declarations: [
    AppComponent,
    SignupComponent,
    LandingComponent,
    ProfileComponent,
    NavbarComponent,
    FooterComponent,
    AboutusComponent,
    UnzipfileComponent
    // DragDropFileComponent,
    // ShowFilesComponent,
    // UploadsfileComponent,
    // LoginComponent
  ],
  imports: [
    BrowserModule,
    NgbModule,
    FormsModule,
    RouterModule,
    AppRoutingModule,
    HomeModule,
    HttpClientModule,
    // LoginModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
