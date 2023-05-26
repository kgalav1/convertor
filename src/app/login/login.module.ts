import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LoginRoutingModule } from './login-routing.module';
import { Routes,RouterModule } from '@angular/router';
import { LoginComponent } from './login.component';

const route: Routes =[
  {path: '', component:LoginComponent}
]
@NgModule({
  declarations: [LoginComponent],
  imports: [
    CommonModule,
    LoginRoutingModule,
    RouterModule.forChild(route),
  ]
})
export class LoginModule { }
