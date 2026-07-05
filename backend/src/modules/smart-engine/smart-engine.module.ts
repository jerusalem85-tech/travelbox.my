import { Module } from '@nestjs/common';
import { SmartEngineService } from './smart-engine.service';
import { SmartEngineController } from './smart-engine.controller';
import { TimelineEngine } from './timeline.engine';
import { LocationEngine } from './location.engine';
import { DateValidationEngine } from './date-validation.engine';
import { CityEngine } from './city.engine';
import { FinancialEngine } from './financial.engine';
import { WorkflowEngine } from './workflow.engine';

@Module({
  controllers: [SmartEngineController],
  providers: [
    SmartEngineService,
    TimelineEngine,
    LocationEngine,
    DateValidationEngine,
    CityEngine,
    FinancialEngine,
    WorkflowEngine,
  ],
  exports: [
    SmartEngineService,
    TimelineEngine,
    LocationEngine,
    DateValidationEngine,
    CityEngine,
    FinancialEngine,
    WorkflowEngine,
  ],
})
export class SmartEngineModule {}
