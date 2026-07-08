import { IsString, IsOptional, IsNumber, IsDateString, IsUUID } from 'class-validator';

export class CreateInsuranceDto {
  @IsUUID()
  tripId: string;

  @IsString()
  provider: string;

  @IsOptional()
  @IsString()
  policyNo?: string;

  @IsString()
  type: string;

  @IsDateString()
  startDate: string;

  @IsDateString()
  endDate: string;

  @IsNumber()
  coverageAmount: number;

  @IsOptional()
  @IsNumber()
  premiumCost?: number;

  @IsOptional()
  @IsNumber()
  sellPrice?: number;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsUUID()
  supplierId?: string;

  @IsOptional()
  @IsString()
  supplierRef?: string;

  @IsOptional()
  @IsString()
  notes?: string;
}

export class UpdateInsuranceDto {
  @IsOptional() @IsString() provider?: string;
  @IsOptional() @IsString() policyNo?: string;
  @IsOptional() @IsString() type?: string;
  @IsOptional() @IsDateString() startDate?: string;
  @IsOptional() @IsDateString() endDate?: string;
  @IsOptional() @IsNumber() coverageAmount?: number;
  @IsOptional() @IsNumber() premiumCost?: number;
  @IsOptional() @IsNumber() sellPrice?: number;
  @IsOptional() @IsString() status?: string;
  @IsOptional() @IsUUID() supplierId?: string;
  @IsOptional() @IsString() supplierRef?: string;
  @IsOptional() @IsString() notes?: string;
}
