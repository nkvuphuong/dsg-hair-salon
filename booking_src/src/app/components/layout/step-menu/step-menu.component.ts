import {Component, Input, OnInit} from '@angular/core';

@Component({
  selector: 'app-step-menu',
  templateUrl: './step-menu.component.html',
  styleUrls: ['./step-menu.component.css']
})
export class StepMenuComponent implements OnInit {

  @Input() step: number;

  constructor() { }

  ngOnInit() {
  }

}
