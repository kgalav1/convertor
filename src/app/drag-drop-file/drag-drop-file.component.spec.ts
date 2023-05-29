import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DragDropFileComponent } from './drag-drop-file.component';

describe('DragDropFileComponent', () => {
  let component: DragDropFileComponent;
  let fixture: ComponentFixture<DragDropFileComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DragDropFileComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DragDropFileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
