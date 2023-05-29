import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UnzipfileComponent } from './unzipfile.component';

describe('UnzipfileComponent', () => {
  let component: UnzipfileComponent;
  let fixture: ComponentFixture<UnzipfileComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UnzipfileComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(UnzipfileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
