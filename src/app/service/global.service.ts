import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Router, ActivatedRoute } from "@angular/router";
import { FormArray } from '@angular/forms';


const baseurl = environment.base_url
const formcontenturl = environment.formcontent_url
@Injectable({
  providedIn: 'root'
})
export class GlobalService {

  constructor(private http: HttpClient, private router: Router) { }

  redirect(url: any = "", pageLoad = true) {
    
    if (url == "login")
    // alert();
      this.clearStorangeData();

    if (pageLoad) {      
      window.location.replace(baseurl + url);
    } else {
      this.router.navigate([url]);
    }
  }
  clearStorangeData() {
    localStorage.clear();
    sessionStorage.clear();
  }
}
