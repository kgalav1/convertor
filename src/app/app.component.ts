import { Component, OnInit, Inject, Renderer2, ElementRef, ViewChild, HostListener } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';
import { GlobalService } from './service/global.service';



@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {    
    title = 'tms';
    isauth:any = null;
    constructor( public global:GlobalService) {}

 
    ngOnInit() {
        // this.isauth = this.global.getAuthData();
        this.isauth = 1;
        if(this.isauth == '' || this.isauth == null || this.isauth == undefined || this.isauth == '0'){
            
            this.global.redirect('login',false)
        }else{
            // this.global.redirect('login',true)
        }
      
    }
}
