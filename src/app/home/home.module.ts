import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { RouterModule } from '@angular/router';

import { HomeComponent } from './home.component';

import { SectionsModule } from '../sections/sections.module';
import { ShowFilesComponent } from '../show-files/show-files.component';
import { DragDropFileComponent } from '../drag-drop-file/drag-drop-file.component';

@NgModule({
    imports: [
        CommonModule,
        BrowserModule,
        FormsModule,
        RouterModule,
        SectionsModule, 
        NgbModule,
        
    ],
    declarations: [ HomeComponent,ShowFilesComponent,DragDropFileComponent ],
    exports:[ HomeComponent ],
    providers: []
})
export class HomeModule { }
